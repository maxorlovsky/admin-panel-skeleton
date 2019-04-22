// 3rd party libs
import * as express from 'express';

// Logger
import { log } from '../inc/log';

// Modules
import LabelsModules from '../modules/labels';

// Interfaces
import { RequestInterface } from '../routing-interfaces';

// Define router
const router: express.Router = express.Router();

router.get('/public/labels', async (req: RequestInterface, res: express.Response): Promise<void> => {
    const attributes = {
        siteId: req.siteId
    };

    const labelsModules = new LabelsModules();
    const labels = await labelsModules.getPublicLabels(attributes);

    // Passing session token to the user
    res.status(200).json({
        data: labels
    });
});

router.get('/labels', async (req: RequestInterface, res: express.Response): Promise<boolean> => {
    if (!req.isLogged) {
        res.status(401).json({ message: 'Authorization error' });

        return false;
    }

    const attributes = {
        siteId: req.siteId
    };

    const labelsModules = new LabelsModules();
    const labels = await labelsModules.getLabels(attributes);

    // Passing session token to the user
    res.status(200).json({
        data: labels
    });
});

router.get('/label/:id', async (req: RequestInterface, res: express.Response): Promise<boolean> => {
    if (!req.isLogged) {
        res.status(401).json({ message: 'Authorization error' });

        return false;
    }

    const attributes = {
        siteId: req.siteId,
        id: parseInt(req.params.id)
    };

    const labelsModules = new LabelsModules();
    const label = await labelsModules.getLabel(attributes);

    // Passing session token to the user
    res.status(200).json({
        data: label
    });
});

router.post('/label', async (req: RequestInterface, res: express.Response): Promise<boolean> => {
    if (!req.isLogged) {
        res.status(401).json({ message: 'Authorization error' });

        return false;
    }

    // Gathering attributes
    const attributes = {
        siteId: req.siteId,
        name: req.body.name.toString(),
        output: req.body.output.toString()
    };

    const labelsModules = new LabelsModules();
    // Trying to add label
    const label = await labelsModules.addLabel(attributes);

    // If there is an error, sending fields and message and logging error
    if (!label) {
        res.status(400).json({
            state: 'error',
            message: labelsModules.getMessage(),
            fields: labelsModules.getFields()
        });

        // Log fail
        await log({
            userId: req.user.id,
            module: 'labels',
            type: 'add',
            info: `Label creation failed <b>${labelsModules.getMessage()}</b>`
        });

        return false;
    }

    // Log success
    await log({
        userId: req.user.id,
        module: 'labels',
        type: 'add',
        info: `Label added <b>${attributes.name}</b>`
    });

    // Passing state
    res.status(200).json({
        state: 'success',
        message: 'Success! New label added.'
    });
});

router.put('/label', async (req: RequestInterface, res: express.Response): Promise<boolean> => {
    if (!req.isLogged) {
        res.status(401).json({ message: 'Authorization error' });

        return false;
    }

    // Gathering attributes
    const attributes = {
        siteId: req.siteId,
        id: parseInt(req.body.id),
        name: req.body.name.toString(),
        output: req.body.output.toString()
    };

    const labelsModules = new LabelsModules();
    // Trying to update label
    const label = await labelsModules.editLabel(attributes);

    // If there is an error, sending fields and message and logging error
    if (!label) {
        res.status(400).json({
            state: 'error',
            message: labelsModules.getMessage(),
            fields: labelsModules.getFields()
        });

        // Log fail
        await log({
            userId: req.user.id,
            module: 'labels',
            type: 'edit',
            info: `Label update failed <b>${labelsModules.getMessage()}</b>`
        });

        return false;
    }

    // Log success
    await log({
        userId: req.user.id,
        module: 'labels',
        type: 'edit',
        info: `Label updated <b>${attributes.name}</b>`
    });

    // Passing state
    res.status(200).json({
        state: 'success',
        message: 'Label updated.'
    });
});

router.delete('/label/:id', async (req: RequestInterface, res: express.Response): Promise<boolean> => {
    if (!req.isLogged) {
        res.status(401).json({ message: 'Authorization error' });

        return false;
    }

    const attributes = {
        id: parseInt(req.params.id)
    };

    const labelsModules = new LabelsModules();
    // Trying to delete label
    const label = await labelsModules.deleteLabel(attributes.id);

    // If there is an error, sending fields and message and logging error
    if (!label) {
        res.status(400).json({
            state: 'error',
            message: labelsModules.getMessage()
        });

        // Log fail
        await log({
            userId: req.user.id,
            module: 'labels',
            type: 'delete',
            info: `Label removal failed [<b>${attributes.id}</b>] <b>${labelsModules.getMessage()}</b>`
        });

        return false;
    }

    // Log success
    await log({
        userId: req.user.id,
        module: 'labels',
        type: 'delete',
        info: `Label removed [<b>${attributes.id}</b>]`
    });

    // Passing state
    res.status(200).json({
        state: 'success',
        message: 'Label removed'
    });
});

export default router;