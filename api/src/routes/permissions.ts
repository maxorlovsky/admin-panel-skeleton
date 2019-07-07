// 3rd party libs
import * as express from 'express';

// Logger
import { log } from '../inc/log';

// Modules
import PermissionsModules from '../modules/permissions';

// Interfaces
import { RequestInterface } from '../routing-interfaces';

// Define router
const router: express.Router = express.Router();

router.get('/permissions', async (req: RequestInterface, res: express.Response): Promise<boolean> => {
    if (!req.isLogged) {
        res.status(401).json({ message: 'Authorization error' });

        return false;
    }

    const permissionsModules = new PermissionsModules();
    const permissions = await permissionsModules.getPermissions();

    // Passing session token to the user
    res.status(200).json({
        data: permissions
    });
});

router.put('/permissions', async (req: RequestInterface, res: express.Response): Promise<boolean> => {
    if (!req.isLogged) {
        res.status(401).json({ message: 'Authorization error' });

        return false;
    }

    const permissionsModules = new PermissionsModules();

    // Trying to update permissions
    const permissions = await permissionsModules.updatePermissions(req.body);

    // If there is an error, sending fields and message and logging error
    if (!permissions) {
        res.status(400).json({
            state: 'error',
            message: permissionsModules.getMessage(),
            fields: permissionsModules.getFields()
        });

        // Log fail
        await log({
            userId: req.user.id,
            module: 'permissions',
            type: 'edit',
            info: `Permissions update failed <b>${permissionsModules.getMessage()}</b>`
        });

        return false;
    }

    // Log success
    await log({
        userId: req.user.id,
        module: 'permissions',
        type: 'edit',
        info: 'Permissions updated'
    });

    // Passing state
    res.status(200).json({
        state: 'success',
        message: 'Permissions updated.'
    });
});

export default router;