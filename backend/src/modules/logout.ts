import { getConnection } from 'typeorm';

// Entities
import { MoUsersAuth } from '../../db/entity/moUsersAuth';
import { MoAdmins } from '../../db/entity/moAdmins';

export default class Logout {
    static async cleanAuth(user: MoAdmins): boolean {
        const response = await getConnection().getRepository(MoUsersAuth)
            .find({
                user: user
            });

        if (response) {
            await getConnection().getRepository(MoUsersAuth).remove(response);
        }

        return true;
    }
}