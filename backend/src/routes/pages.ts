import * as express from 'express';

import PagesModules from '../modules/pages';
import { log } from '../inc/log';

// Define router
const router: express.Router = express.Router();

router.get('/public/pages', async (req: express.Request, res: express.Response): JSON => {
    const attributes = {
        siteId: req.siteId
    };

    const pagesModules = new PagesModules();
    const pages = await pagesModules.getPublicPages(attributes);

    // Passing session token to the user
    res.status(200).json({
        data: pages
    });
});

router.get('/pages', async (req: express.Request, res: express.Response): JSON => {
    if (!req.isLogged) {
        res.status(401).json({ message: 'Authorization error' });

        return false;
    }

    const attributes = {
        siteId: req.siteId
    };

    const pagesModules = new PagesModules();
    const pages = await pagesModules.getPages(attributes);

    // Passing session token to the user
    res.status(200).json({
        data: pages
    });
});

router.get('/page/:id', async (req: express.Request, res: express.Response): JSON => {
    if (!req.isLogged) {
        res.status(401).json({ message: 'Authorization error' });

        return false;
    }

    const attributes = {
        siteId: req.siteId,
        id: parseInt(req.params.id)
    };

    const pagesModules = new PagesModules();
    const page = await pagesModules.getPage(attributes);

    // Passing session token to the user
    res.status(200).json({
        data: page
    });
});

router.post('/page', async (req: express.Request, res: express.Response): JSON => {
    if (!req.isLogged) {
        res.status(401).json({ message: 'Authorization error' });

        return false;
    }

    // Gathering attributes
    const attributes = {
        siteId: req.siteId,
        title: req.body.title.toString(),
        metaTitle: req.body.metaTitle.toString(),
        metaDescription: req.body.metaDescription.toString(),
        link: req.body.link.toString(),
        text: req.body.text.toString(),
        enabled: req.body.enabled
    };

    const pagesModules = new PagesModules();
    // Trying to add page
    const page = await pagesModules.addPage(attributes);

    // If there is an error, sending fields and message and logging error
    if (!page) {
        res.status(400).json({
            state: 'error',
            message: pagesModules.getMessage(),
            fields: pagesModules.getFields()
        });

        // Log fail
        await log({
            userId: req.user.id,
            module: 'pages',
            type: 'add',
            info: `Page creation failed <b>${pagesModules.getMessage()}</b>`
        });

        return false;
    }

    // Log success
    await log({
        userId: req.user.id,
        module: 'pages',
        type: 'add',
        info: `Page added <b>${attributes.title}</b>`
    });

    // Passing state
    res.status(200).json({
        state: 'success',
        message: 'Success! New page added.'
    });
});

router.put('/page', async (req: express.Request, res: express.Response): JSON => {
    if (!req.isLogged) {
        res.status(401).json({ message: 'Authorization error' });

        return false;
    }

    // Gathering attributes
    const attributes = {
        id: parseInt(req.body.id),
        title: req.body.title.toString(),
        metaTitle: req.body.metaTitle.toString(),
        metaDescription: req.body.metaDescription.toString(),
        link: req.body.link.toString(),
        text: req.body.text.toString(),
        enabled: req.body.enabled
    };

    const pagesModules = new PagesModules();
    // Trying to update page
    const page = await pagesModules.editPage(attributes);

    // If there is an error, sending fields and message and logging error
    if (!page) {
        res.status(400).json({
            state: 'error',
            message: pagesModules.getMessage(),
            fields: pagesModules.getFields()
        });

        // Log fail
        await log({
            userId: req.user.id,
            module: 'pages',
            type: 'edit',
            info: `Page update failed <b>${pagesModules.getMessage()}</b>`
        });

        return false;
    }

    // Log success
    await log({
        userId: req.user.id,
        module: 'pages',
        type: 'edit',
        info: `Page updated <b>${attributes.title}</b>`
    });

    // Passing state
    res.status(200).json({
        state: 'success',
        message: 'Page updated.'
    });
});

router.delete('/page/:id', async (req: express.Request, res: express.Response): JSON => {
    if (!req.isLogged) {
        res.status(401).json({ message: 'Authorization error' });

        return false;
    }

    const attributes = {
        id: parseInt(req.params.id)
    };

    const pagesModules = new PagesModules();
    // Trying to update page
    const page = await pagesModules.deletePage(attributes.id);

    // If there is an error, sending fields and message and logging error
    if (!page) {
        res.status(400).json({
            state: 'error',
            message: pagesModules.getMessage()
        });

        // Log fail
        await log({
            userId: req.user.id,
            module: 'pages',
            type: 'delete',
            info: `Page removal failed [<b>${attributes.id}</b>] <b>${pagesModules.getMessage()}</b>`
        });

        return false;
    }

    // Log success
    await log({
        userId: req.user.id,
        module: 'pages',
        type: 'delete',
        info: `Page removed [<b>${attributes.id}</b>]`
    });

    // Passing state
    res.status(200).json({
        state: 'success',
        message: 'Page removed'
    });
});

export default router;