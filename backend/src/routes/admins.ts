import * as express from 'express';

import AdminsModules from '../modules/admins';
import { log } from '../inc/log';

// Define router
const router: express.Router = express.Router();

router.get('/admins', async (req: express.Request, res: express.Response): JSON => {
    if (!req.isLogged) {
        res.status(401).json({ message: 'Authorization error' });

        return false;
    }

    const adminsModules = new AdminsModules(req.user);
    const admins = await adminsModules.getAdmins();

    // Passing session token to the user
    res.status(200).json({
        data: admins
    });
});

router.get('/admin/:id', async (req: express.Request, res: express.Response): JSON => {
    if (!req.isLogged) {
        res.status(401).json({ message: 'Authorization error' });

        return false;
    }

    const attributes = {
        id: parseInt(req.params.id)
    };

    const adminsModules = new AdminsModules(req.user);
    const admin = await adminsModules.getAdmin(attributes.id);

    // Passing session token to the user
    res.status(200).json({
        data: admin
    });
});

router.post('/admin', async (req: express.Request, res: express.Response): JSON => {
    if (!req.isLogged) {
        res.status(401).json({ message: 'Authorization error' });

        return false;
    }

    // Gathering attributes
    const attributes = {
        login: req.body.login.toString(),
        password: req.body.password.toString(),
        level: parseInt(req.body.level),
        permissions: req.body.permissions
    };

    const adminsModules = new AdminsModules(req.user);
    // Trying to add page
    const admin = await adminsModules.addAdmin(attributes);

    // If there is an error, sending fields and message and logging error
    if (!admin) {
        res.status(400).json({
            state: 'error',
            message: adminsModules.getMessage(),
            fields: adminsModules.getFields()
        });

        // Log fail
        await log({
            userId: req.user.id,
            module: 'admins',
            type: 'add',
            info: `Admin creation failed <b>${adminsModules.getMessage()}</b>`
        });

        return false;
    }

    // Log success
    await log({
        userId: req.user.id,
        module: 'admins',
        type: 'add',
        info: `Admin added <b>${attributes.login}</b>`
    });

    // Passing state
    res.status(200).json({
        state: 'success',
        message: 'Success! New admin is created.'
    });
});

router.put('/admin', async (req: express.Request, res: express.Response): JSON => {
    if (!req.isLogged) {
        res.status(401).json({ message: 'Authorization error' });

        return false;
    }

    // Gathering attributes
    const attributes = {
        id: parseInt(req.body.id),
        login: req.body.login.toString(),
        password: req.body.password.toString(),
        level: parseInt(req.body.level),
        permissions: req.body.permissions
    };

    const adminsModules = new AdminsModules(req.user);
    // Trying to update page
    const admin = await adminsModules.editAdmin(attributes);

    // If there is an error, sending fields and message and logging error
    if (!admin) {
        res.status(400).json({
            state: 'error',
            message: adminsModules.getMessage(),
            fields: adminsModules.getFields()
        });

        // Log fail
        await log({
            userId: req.user.id,
            module: 'admins',
            type: 'edit',
            info: `Admin update failed [<b>${attributes.id}</b>] <b>${adminsModules.getMessage()}</b>`
        });

        return false;
    }

    // Log success
    await log({
        userId: req.user.id,
        module: 'admins',
        type: 'edit',
        info: `Admin updated <b>${attributes.id}</b>`
    });

    // Passing state
    res.status(200).json({
        state: 'success',
        message: 'Success! Admin updated.'
    });
});

router.delete('/admin/:id', async (req: express.Request, res: express.Response): JSON => {
    if (!req.isLogged) {
        res.status(401).json({ message: 'Authorization error' });

        return false;
    }

    const attributes = {
        id: parseInt(req.params.id)
    };

    const adminsModules = new AdminsModules(req.user);
    // Trying to delete admin
    const admin = await adminsModules.deleteAdmin(attributes.id);

    // If there is an error, sending fields and message and logging error
    if (!admin) {
        res.status(400).json({
            state: 'error',
            message: adminsModules.getMessage()
        });

        // Log fail
        await log({
            userId: req.user.id,
            module: 'admins',
            type: 'delete',
            info: `Admin removal failed [<b>${attributes.id}</b>] <b>${adminsModules.getMessage()}</b>`
        });

        return false;
    }

    // Log success
    await log({
        userId: req.user.id,
        module: 'admins',
        type: 'delete',
        info: `Admin removed [<b>${attributes.id}</b>]`
    });

    // Passing state
    res.status(200).json({
        state: 'success',
        message: 'Admin removed'
    });
});

export default router;