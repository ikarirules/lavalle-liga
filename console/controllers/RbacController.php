<?php
namespace console\controllers;

use yii\console\Controller;
use Yii;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        // --- PERMISOS ---
        $verJugadores = $auth->createPermission('verJugadores');
        $verJugadores->description = 'Ver lista de jugadores';
        $auth->add($verJugadores);

        $editarJugadores = $auth->createPermission('editarJugadores');
        $editarJugadores->description = 'Crear y editar jugadores';
        $auth->add($editarJugadores);

        $verArbitros = $auth->createPermission('verArbitros');
        $verArbitros->description = 'Ver lista de arbitros';
        $auth->add($verArbitros);

        $editarArbitros = $auth->createPermission('editarArbitros');
        $editarArbitros->description = 'Crear y editar arbitros';
        $auth->add($editarArbitros);

        $verClubes = $auth->createPermission('verClubes');
        $verClubes->description = 'Ver lista de clubes';
        $auth->add($verClubes);

        $editarClubes = $auth->createPermission('editarClubes');
        $editarClubes->description = 'Crear y editar clubes';
        $auth->add($editarClubes);

        $gestionarLiga = $auth->createPermission('gestionarLiga');
        $gestionarLiga->description = 'Administrar toda la liga';
        $auth->add($gestionarLiga);

        // --- ROLES ---

        // Jugador: puede verse a si mismo y ver otros jugadores/clubes
        $jugador = $auth->createRole('jugador');
        $jugador->description = 'Jugador de un club';
        $auth->add($jugador);
        $auth->addChild($jugador, $verJugadores);
        $auth->addChild($jugador, $verClubes);

        // Arbitro: independiente de club
        $arbitro = $auth->createRole('arbitro');
        $arbitro->description = 'Arbitro de la liga';
        $auth->add($arbitro);
        $auth->addChild($arbitro, $verJugadores);
        $auth->addChild($arbitro, $verClubes);
        $auth->addChild($arbitro, $verArbitros);

        // Directivo: gestiona jugadores de su club
        $directivo = $auth->createRole('directivo');
        $directivo->description = 'Directivo de un club';
        $auth->add($directivo);
        $auth->addChild($directivo, $verJugadores);
        $auth->addChild($directivo, $editarJugadores);
        $auth->addChild($directivo, $verClubes);

        // Miembro de la liga: gestiona arbitros y clubes
        $miembroLiga = $auth->createRole('miembro_liga');
        $miembroLiga->description = 'Miembro de la liga (independiente de club)';
        $auth->add($miembroLiga);
        $auth->addChild($miembroLiga, $verJugadores);
        $auth->addChild($miembroLiga, $verArbitros);
        $auth->addChild($miembroLiga, $editarArbitros);
        $auth->addChild($miembroLiga, $verClubes);
        $auth->addChild($miembroLiga, $editarClubes);

        // Admin: acceso total, hereda todo
        $admin = $auth->createRole('admin_liga');
        $admin->description = 'Administrador de la liga';
        $auth->add($admin);
        $auth->addChild($admin, $miembroLiga);
        $auth->addChild($admin, $directivo);
        $auth->addChild($admin, $editarJugadores);
        $auth->addChild($admin, $gestionarLiga);

        // Asignar rol admin al usuario con ID 1
        $auth->assign($admin, 1);

        echo "RBAC inicializado correctamente.\n";
    }
}
