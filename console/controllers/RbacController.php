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

        // Arbitro: solo puede ver jugadores, cargar partidos y crear informes
        $arbitro = $auth->createRole('arbitro');
        $arbitro->description = 'Arbitro de la liga';
        $auth->add($arbitro);
        $auth->addChild($arbitro, $verJugadores);

        // Directivo: gestiona jugadores de su club
        $directivo = $auth->createRole('directivo');
        $directivo->description = 'Directivo de un club';
        $auth->add($directivo);
        $auth->addChild($directivo, $verJugadores);
        $auth->addChild($directivo, $editarJugadores);
        $auth->addChild($directivo, $verClubes);

        // Miembro de la liga: gestiona arbitros, clubes y cualquier jugador
        $miembroLiga = $auth->createRole('miembro_liga');
        $miembroLiga->description = 'Miembro de la liga (independiente de club)';
        $auth->add($miembroLiga);
        $auth->addChild($miembroLiga, $verJugadores);
        $auth->addChild($miembroLiga, $editarJugadores);
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

    /**
     * Asigna un rol a un usuario.
     * Uso: php yii rbac/assign <user_id> <rol>
     * Ejemplo: php yii rbac/assign 2 arbitro
     */
    public function actionAssign($userId, $role)
    {
        $auth = Yii::$app->authManager;

        $roleObj = $auth->getRole($role);
        if (!$roleObj) {
            echo "Error: el rol '$role' no existe.\n";
            echo "Roles disponibles: jugador, arbitro, directivo, miembro_liga, admin_liga\n";
            return 1;
        }

        $user = \common\models\User::findOne($userId);
        if (!$user) {
            echo "Error: no existe un usuario con ID $userId.\n";
            return 1;
        }

        // Quitar asignaciones previas
        $auth->revokeAll($userId);

        $auth->assign($roleObj, $userId);
        echo "Rol '$role' asignado a {$user->username} (ID: $userId).\n";
    }

    /**
     * Muestra todos los usuarios con sus roles asignados.
     * Uso: php yii rbac/list
     */
    public function actionList()
    {
        $users = \common\models\User::find()->all();
        $auth  = Yii::$app->authManager;

        echo str_pad('ID', 6) . str_pad('Usuario', 25) . "Rol\n";
        echo str_repeat('-', 50) . "\n";

        foreach ($users as $user) {
            $roles = $auth->getRolesByUser($user->id);
            $roleNames = $roles ? implode(', ', array_keys($roles)) : '(sin rol)';
            echo str_pad($user->id, 6) . str_pad($user->username, 25) . $roleNames . "\n";
        }
    }
}
