// 3rd party libs
import { getConnection } from 'typeorm';

// Classes
import SharedComponents from '../shared-components';

// Interfaces
import { PermissionsFormInterface } from '../interfaces/permissions';

// Entities
import { Mo } from '../../db/entity/mo';

export default class Permissions extends SharedComponents {

    public async getPermissions(): Promise<Array<string>> {
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

    public async updatePermissions(attributes: Array<PermissionsFormInterface>): Promise<boolean> {
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

    private checkForm(attributes: Array<PermissionsFormInterface>): boolean {
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