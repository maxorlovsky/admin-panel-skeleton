// Express
import * as express from 'express';

// Create DB connection
import './inc/db';

// Import CORS rules
import cors from './inc/cors';

// Middlewares
import authMiddleware from './middleware/auth';
import multisiteMiddleware from './middleware/multisite';

// Routes
import adminsRoutes from './routes/admins';
import labelsRoutes from './routes/labels';
import loginRoutes from './routes/login';
import logoutRoutes from './routes/logout';
import logsRoutes from './routes/logs';
import pagesRoutes from './routes/pages';
import permissionsRoutes from './routes/permissions';
import userDataRoutes from './routes/user-data';
import homeRoutes from './routes/home';

// Create a new express application instance
const app: express.Application = express();

// Apply CORS config
app.use(cors);

app.use(express.json());

// Apply middleware
app.use(authMiddleware);
app.use(multisiteMiddleware);

// Define static files
app.use(express.static('public'));

// Initiate the routes
app.use('/', adminsRoutes);
app.use('/', labelsRoutes);
app.use('/', loginRoutes);
app.use('/', logoutRoutes);
app.use('/', logsRoutes);
app.use('/', pagesRoutes);
app.use('/', permissionsRoutes);
app.use('/', userDataRoutes);
app.use('/', homeRoutes);

app.listen(3000, (): void => {
    // eslint-disable-next-line
    console.log('App listening on port 3000!');
});