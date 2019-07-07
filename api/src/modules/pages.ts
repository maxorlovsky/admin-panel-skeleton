// 3rd party libs
import { getConnection } from 'typeorm';
import * as decode from 'unescape';

// Classes
import SharedComponents from '../shared-components';

// Interfaces
import { PagesFormInterface, GetPageInterface } from '../interfaces/pages';

// Entities
import { MoPages } from '../../db/entity/moPages';

export default class Pages extends SharedComponents {
    public async getPublicPages(attributes: GetPageInterface): Promise<Array<PagesFormInterface>> {
        const returnPages = [];

        try {
            // Fetching previous attempts to login
            const pages = await getConnection().getRepository(MoPages)
                .find({
                    where: {
                        siteId: attributes.siteId,
                        deleted: false,
                        enabled: true
                    },
                    order: {
                        title: 'ASC'
                    }
                });

            for (const page of pages) {
                returnPages.push({
                    title: page.title,
                    metaTitle: page.metaTitle ? page.metaTitle : page.title,
                    metaDescription: page.metaDescription,
                    link: page.link,
                    text: decode(page.text)
                });
            }
        } catch (error) {
            console.error(error);
        }

        return returnPages;
    }

    public async getPages(attributes: GetPageInterface): Promise<Array<PagesFormInterface>> {
        const returnPages = [];

        try {
            // Fetching previous attempts to login
            const pages: Array<MoPages> = await getConnection().getRepository(MoPages)
                .find({
                    where: {
                        siteId: attributes.siteId,
                        deleted: false
                    },
                    order: {
                        title: 'ASC'
                    }
                });

            for (const page of pages) {
                returnPages.push({
                    id: page.id,
                    title: page.title,
                    metaTitle: page.metaTitle,
                    metaDescription: page.metaDescription,
                    link: page.link,
                    text: decode(page.text),
                    enabled: page.enabled
                });
            }
        } catch (error) {
            console.error(error);
        }

        return returnPages;
    }

    public async getPage(attributes: GetPageInterface): Promise<PagesFormInterface> {
        let returnPage = null;

        try {
            // Fetching previous attempts to login
            const page: MoPages = await getConnection().getRepository(MoPages)
                .findOne({
                    siteId: attributes.siteId,
                    deleted: false,
                    id: attributes.id
                });

            if (page) {
                returnPage = {
                    id: page.id,
                    title: page.title,
                    metaTitle: page.metaTitle,
                    metaDescription: page.metaDescription,
                    link: page.link,
                    text: decode(page.text),
                    enabled: page.enabled
                };
            }
        } catch (error) {
            console.error(error);
        }

        return returnPage;
    }

    public async addPage(attributes: PagesFormInterface): Promise<boolean> {
        const formData = await this.checkForm(attributes);

        if (!formData) {
            return false;
        }

        const newPage = new MoPages();

        newPage.siteId = attributes.siteId;
        newPage.title = attributes.title;
        newPage.metaTitle = attributes.metaTitle;
        newPage.metaDescription = attributes.metaDescription;
        newPage.link = attributes.link;
        newPage.text = attributes.text;
        newPage.enabled = attributes.enabled;

        try {
            await getConnection().getRepository(MoPages).save(newPage);
        } catch (error) {
            console.error(error);

            return false;
        }

        return true;
    }

    public async editPage(attributes: PagesFormInterface): Promise<boolean> {
        const formData = await this.checkForm(attributes);

        if (!formData) {
            return false;
        }

        try {
            const page: MoPages = await getConnection().getRepository(MoPages)
                .findOne({
                    id: attributes.id
                });

            page.title = attributes.title;
            page.metaTitle = attributes.metaTitle;
            page.metaDescription = attributes.metaDescription;
            page.link = attributes.link;
            page.text = attributes.text;
            page.enabled = attributes.enabled;

            await getConnection().getRepository(MoPages).save(page);
        } catch (error) {
            console.error(error);

            return false;
        }

        return true;
    }

    public async deletePage(id: number): Promise<boolean> {
        try {
            const page = await getConnection().getRepository(MoPages)
                .findOne({
                    id: id
                });

            page.deleted = true;

            await getConnection().getRepository(MoPages).save(page);
        } catch (error) {
            console.error(error);

            return false;
        }

        return true;
    }

    private checkForm(attributes: PagesFormInterface): boolean {
        if (!attributes.title) {
            this.message += 'Title is empty<br />';
            this.fields.push('title');
        } else if (attributes.title.length > 100) {
            this.message += 'Title is too long<br />';
            this.fields.push('title');
        }

        if (attributes.metaTitle && attributes.metaTitle.length > 70) {
            this.message += 'Meta title is too long<br />';
            this.fields.push('metaTitle');
        }

        if (attributes.metaDescription && attributes.metaDescription.length > 230) {
            this.message += 'Meta description is too long<br />';
            this.fields.push('metaDescription');
        }

        if (!attributes.link) {
            this.message += 'Link is empty<br />';
            this.fields.push('link');
        } else if (attributes.link.length > 300) {
            this.message += 'Link is too long<br />';
            this.fields.push('link');
        } else if (attributes.link.match(/\s/g)) {
            this.message += 'Link must not have spaces<br />';
            this.fields.push('link');
        }

        if (this.message) {
            return false;
        }

        return true;
    }
}