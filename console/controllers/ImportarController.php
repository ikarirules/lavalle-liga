<?php

namespace console\controllers;

use common\models\Jugador;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Importa jugadores desde archivos Excel con el formato estándar de la liga.
 *
 * Formato esperado del Excel (columnas en orden):
 *   Col 1: N° (ignorado)
 *   Col 2: Apellido y Nombre
 *   Col 3: DNI
 *   Col 4: Clase (año de nacimiento como serial Excel o texto)
 *   Col 5: N° Carnet
 *   Col 6: Cat  → categoria_id en la BD
 *   Col 7: Club → club_id en la BD
 */
class ImportarController extends Controller
{
    /**
     * Importa jugadores desde un Excel.
     *
     * Uso:
     *   php yii importar/jugadores <archivo.xlsx>
     *
     * Ejemplo:
     *   php yii importar/jugadores C:/ruta/VeteranosTresdeMayo.xlsx
     */
    public function actionJugadores(string $archivo): int
    {
        $path = \Yii::getAlias($archivo);

        if (!file_exists($path)) {
            $this->stderr("Error: no se encontró el archivo '$path'\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $spreadsheet = IOFactory::load($path);
        $ws          = $spreadsheet->getActiveSheet();
        $rows        = $ws->toArray(null, true, false, false);

        // Saltar encabezado (fila 0)
        array_shift($rows);

        $insertados = 0;
        $omitidos   = 0;
        $errores    = 0;

        foreach ($rows as $i => $row) {
            $fila = $i + 2; // número de fila real en el Excel

            $nombre      = trim((string)($row[1] ?? ''));
            $dni         = trim((string)($row[2] ?? ''));
            $clase       = $row[3] ?? null;
            $carnet      = trim((string)($row[4] ?? ''));
            $categoriaId = (int)($row[5] ?? 0);
            $clubId      = (int)($row[6] ?? 0);

            if ($nombre === '' || $dni === '') {
                continue; // fila vacía al final del archivo
            }

            // Convertir "Clase" a fecha de nacimiento (1 de enero del año)
            $fechaNac = $this->convertirClase($clase);

            // Verificar duplicado por DNI
            if (Jugador::find()->where(['dni' => $dni])->exists()) {
                $this->stdout("  [OMITIDO] Fila $fila: '$nombre' (DNI $dni ya existe)\n");
                $omitidos++;
                continue;
            }

            $jugador                         = new Jugador();
            $jugador->nombre                 = $nombre;
            $jugador->dni                    = $dni;
            $jugador->fecha_nacimiento       = $fechaNac;
            $jugador->numero_carnet          = $carnet !== '' ? $carnet : null;
            $jugador->categoria_id           = $categoriaId;
            $jugador->club_id                = $clubId;
            $jugador->cant_fechas_suspension = 0;

            if ($jugador->save()) {
                $this->stdout("  [OK]      Fila $fila: '$nombre' (DNI $dni)\n");
                $insertados++;
            } else {
                $errMsg = implode(', ', $jugador->getFirstErrors());
                $this->stderr("  [ERROR]   Fila $fila: '$nombre' — $errMsg\n");
                $errores++;
            }
        }

        $this->stdout("\nResumen: $insertados insertados, $omitidos omitidos, $errores errores.\n");

        return $errores > 0 ? ExitCode::UNSPECIFIED_ERROR : ExitCode::OK;
    }

    /**
     * Convierte la columna "Clase" (año de nacimiento) a una fecha Y-m-d.
     * Acepta serial Excel (float/int) o un año de 4 dígitos como texto.
     */
    private function convertirClase($valor): ?string
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        // Si es numérico, tratar como serial Excel
        if (is_numeric($valor)) {
            $serial = (float)$valor;

            // Si parece un año de 4 dígitos (1900-2100), usar enero 1
            if ($serial >= 1900 && $serial <= 2100 && floor($serial) === $serial) {
                return (int)$serial . '-01-01';
            }

            // Es un serial de fecha Excel
            $date = ExcelDate::excelToDateTimeObject($serial);
            // Guardar solo el año como 1 de enero (la "Clase" es el año de nacimiento)
            return $date->format('Y') . '-01-01';
        }

        // Si es texto con un año de 4 dígitos
        if (preg_match('/^\d{4}$/', trim((string)$valor))) {
            return trim((string)$valor) . '-01-01';
        }

        // Intentar parsear como fecha directamente
        $ts = strtotime((string)$valor);
        return $ts !== false ? date('Y-m-d', $ts) : null;
    }
}
