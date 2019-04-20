// Get config data
import * as config from './config.json';

export default (req: express.Request, res: express.Response, next: express.Next): void => {
    if (config.corsDomains.indexOf(req.headers.origin) > -1) {
        res.header('Access-Control-Allow-Origin', req.headers.origin);
    }

    res.header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH');
    res.header('Access-Control-Allow-Headers', `Origin, X-Requested-With, Content-Type, Accept, ${config.corsHeaders}`);

    next();
};