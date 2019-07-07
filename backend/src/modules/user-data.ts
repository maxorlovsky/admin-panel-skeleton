// 3rd party libs
import { getConnection } from 'typeorm';
import SharedComponents from '../shared-components';

// Config
import * as config from '../inc/config.json';

// Modules
import Login from './login';
import Admins from './admins';

// Interfaces
import { AdminInterface } from '../interfaces/admins';
import { PasswordFormInterface, MultisiteInterface, MenuInterface } from '../interfaces/user-data';

// Entities
import { Mo } from '../../db/entity/mo';
import { MoMultisite } from '../../db/entity/moMultisite';
import { MoAdmins } from '../../db/entity/moAdmins';

export default class UserData extends SharedComponents {
    private user: AdminInterface;

    constructor(user) {
        super();

        this.user = user;
    }

    public fetchAdminData(): AdminInterface | null {
        if (!this.user) {
            return null;
        }

        return this.user;
    }

    public async fetchMenu(): Promise<Array<MenuInterface>> {
        // Fetching menu json from database
        const menu = await getConnection().getRepository(Mo)
            .findOne({
                setting: 'menu'
            });

        // Converting menu to proper json
        const jsonMenu = JSON.parse(menu.value);

        const returnMenu = [];

        for (const menu of jsonMenu) {
            // Check if user level is allowing him to access the page, if not, removing it from navigation
            if (menu.level <= this.user.level || (this.user.level === 0 && this.user.customAccess.indexOf(menu.key) >= 0)) {
                returnMenu.push({
                    url: `/${menu.key}`,
                    title: menu.name,
                    icon: menu.iconClasses
                });

                // Check if there is subpages
                if (menu.subCategories.length) {
                    const index = returnMenu.length - 1;

                    returnMenu[index].sublinks = [];

                    for (const subMenu of menu.subCategories) {
                        // Check if user level is allowing him to access the sub page, if not, removing it from navigation
                        if (subMenu.level <= this.user.level || (this.user.level === 0 && this.user.customAccess.indexOf(subMenu.key) >= 0)) {
                            returnMenu[index].sublinks.push({
                                url: `/${subMenu.key}`,
                                title: subMenu.name,
                                icon: subMenu.iconClasses
                            });
                        }
                    }
                }
            }
        }

        return returnMenu;
    }

    public async fetchMultisites(): Promise<Array<MultisiteInterface>> {
        // Fetching multisites from database
        const multisites = await getConnection().getRepository(MoMultisite).find();

        // If not found, returning false
        if (!multisites.length) {
            return [];
        }

        const gatherMultisites = [];

        for (const site of multisites) {
            gatherMultisites.push({
                id: site.id,
                name: site.name
            });
        }

        return gatherMultisites;
    }

    public async updatePassword(attributes: PasswordFormInterface): Promise<boolean> {
        if (!await this.checkFormPassword(attributes)) {
            return false;
        }

        try {
            const admin = await getConnection().getRepository(MoAdmins)
                .findOne({
                    id: this.user.id
                });

            admin.password = await Admins.passwordConvert(attributes.newPass);

            await getConnection().getRepository(MoAdmins).save(admin);
        } catch (error) {
            console.error(error);

            return false;
        }

        return true;
    }

    private async checkFormPassword(attributes: PasswordFormInterface): Promise<boolean> {
        if (!attributes.currentPass) {
            this.message += 'Current Password is empty<br />';
            this.fields.push('currentPass');
        }

        if (!attributes.newPass) {
            this.message += 'New Password is empty<br />';
            this.fields.push('newPass');
        }

        if (!attributes.repeatPass) {
            this.message += 'Repeat Password is empty<br />';
            this.fields.push('repeatPass');
        }

        if (attributes.newPass.length < config.minPasswordLength) {
            this.message += `New Password must be at least ${config.minPasswordLength} characters long<br />`;
            this.fields.push('newPass');
        }

        if (attributes.newPass !== attributes.repeatPass) {
            this.message += 'Passwords does not match<br />';
            this.fields.push('repeatPass');
        }

        const checkPassword = await getConnection().getRepository(MoAdmins)
            .findOne({
                id: this.user.id
            });

        if (!await Login.passwordVerify(attributes.currentPass, checkPassword.password.replace('$2y$', '$2a$'))) {
            this.message += 'Current password is incorrect<br />';
            this.fields.push('currentPass');
        }

        // In case if there are any messages, return false
        if (this.message) {
            return false;
        }

        return true;
    }
}