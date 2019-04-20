import * as express from 'express';

// Define router
const router: express.Router = express.Router();

router.all('/*', (req: express.Request, res: express.Response): JSON => {
    res.status(403).json({ message: '*Beep-Boop* nothing to see here' });
});

export default router;