import * as express from 'express';

import LogsModules from '../modules/logs';

// Define router
const router: express.Router = express.Router();

router.get('/logs', async (req: express.Request, res: express.Response): JSON => {
    if (!req.isLogged) {
        res.status(401).json({ message: 'Authorization error' });

        return false;
    }

    const attributes = {
        module: req.query.module ? req.query.module.toString() : '',
        type: req.query.type ? req.query.type.toString() : '',
        page: parseInt(req.query.page)
    };

    const logsModules = new LogsModules();
    const logs = await logsModules.getLogs(attributes);

    // Passing session token to the user
    res.status(200).json({
        data: logs.logs,
        amount: logs.amount
    });
});

export default router;