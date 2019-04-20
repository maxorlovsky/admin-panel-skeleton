import { getConnection } from 'typeorm';

// Classes
import SharedComponents from '../shared-components';

// Entities
import { Mo } from '../../db/entity/mo';

export default class Permissions extends SharedComponents {

    public async getPermissions(): array {
        let returnPermissions = [];

        try {
            // Fetching previous attempts to login
            const permissions = await getConnection().getRepository(Mo)
                .findOne({
                    setting: 'menu'
                });

            if (permissions) {
                returnPermissions = JSON.parse(permissions.value);
            }
        } catch (error) {
            console.error(error);
        }

        return returnPermissions;
    }

    public async updatePermissions(attributes: array): boolean {
        const formData = await this.checkForm(attributes);

        if (!formData) {
            return false;
        }

        try {
            const permissions = await getConnection().getRepository(Mo)
                .findOne({
                    setting: 'menu'
                });

            permissions.value = JSON.stringify(attributes);

            await getConnection().getRepository(Mo).save(permissions);
        } catch (error) {
            console.error(error);

            return false;
        }

        return true;
    }

    private checkForm(attributes: array): boolean {
        for (const attribute of attributes) {
            if (!attribute.name) {
                this.message += `${attribute.key.charAt(0).toUpperCase()}${attribute.key.slice(1)} name is empty<br />`;
                this.fields.push(attribute.key);
            }

            if (!attribute.level) {
                this.message += `${attribute.key.charAt(0).toUpperCase()}${attribute.key.slice(1)} level is empty<br />`;
                this.fields.push(attribute.key);
            }
        }

        if (this.message) {
            return false;
        }

        return true;
    }
}