<?php

namespace console\controllers;

use common\models\Jugador;
use common\models\User;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Yii;
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
     * Importa usuarios árbitro desde un Excel, ya con la cuenta activada.
     *
     * Convención de la liga (sin drama, ya usada antes):
     *   - username = nombre-apellido (minúsculas, sin tildes, espacios → guion)
     *   - password = DNI
     *   - email = placeholder "{username}@arbitro.local" (la columna es
     *     NOT NULL UNIQUE en la BD aunque el modelo User no la valide)
     *   - status = ACTIVE (no INACTIVE como en el signup normal)
     *   - se asigna el rol RBAC 'arbitro' automáticamente
     *   - no se guarda columna de nombre/apellido separada: el modelo User
     *     no tiene esa columna y no se agrega una nueva, el nombre queda
     *     representado en el propio username.
     *
     * Formato esperado del Excel (columnas en orden):
     *   Col 1: Nombre y Apellido
     *   Col 2: DNI
     *
     * Uso:
     *   php yii importar/arbitros <archivo.xlsx>
     */
    public function actionArbitros(string $archivo): int
    {
        $path = \Yii::getAlias($archivo);

        if (!file_exists($path)) {
            $this->stderr("Error: no se encontró el archivo '$path'\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $auth = Yii::$app->authManager;
        $rolArbitro = $auth->getRole('arbitro');
        if (!$rolArbitro) {
            $this->stderr("Error: el rol 'arbitro' no existe. Corré 'php yii rbac/init' primero.\n");
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

            $nombre = trim((string)($row[0] ?? ''));
            $dni    = trim((string)($row[1] ?? ''));

            if ($nombre === '' || $dni === '') {
                continue; // fila vacía al final del archivo
            }

            $username = $this->slugUsername($nombre);

            // Verificar duplicado por username
            if (User::find()->where(['username' => $username])->exists()) {
                $this->stdout("  [OMITIDO] Fila $fila: '$nombre' (usuario '$username' ya existe)\n");
                $omitidos++;
                continue;
            }

            $user           = new User();
            $user->username = $username;
            $user->email    = $username . '@arbitro.local'; // la columna es NOT NULL UNIQUE, no hay email real
            $user->tipo     = 'arbitro';
            $user->status   = User::STATUS_ACTIVE;
            $user->setPassword($dni);
            $user->generateAuthKey();

            if ($user->save()) {
                $auth->assign($rolArbitro, $user->id);
                $this->stdout("  [OK]      Fila $fila: '$nombre' → usuario '$username' / pass $dni (activado, rol arbitro)\n");
                $insertados++;
            } else {
                $errMsg = implode(', ', $user->getFirstErrors());
                $this->stderr("  [ERROR]   Fila $fila: '$nombre' — $errMsg\n");
                $errores++;
            }
        }

        $this->stdout("\nResumen: $insertados insertados, $omitidos omitidos, $errores errores.\n");

        return $errores > 0 ? ExitCode::UNSPECIFIED_ERROR : ExitCode::OK;
    }

    /**
     * Convierte "Nombre y Apellido" en un username tipo "nombre-apellido":
     * minúsculas, sin tildes, espacios (y repetidos) colapsados a un guion.
     */
    private function slugUsername(string $nombreCompleto): string
    {
        $sinTildes = strtr($nombreCompleto, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u', 'Ñ' => 'n',
            'ü' => 'u', 'Ü' => 'u',
        ]);

        $slug = mb_strtolower(trim($sinTildes));
        $slug = preg_replace('/\s+/', '-', $slug);
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);

        return $slug;
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
