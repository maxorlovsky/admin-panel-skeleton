// 3rd party libs
import { getConnection, Not } from 'typeorm';
import * as decode from 'unescape';

// Classes
import SharedComponents from '../shared-components';

// Interfaces
import { GetLabelInterface, LabelsFormInterface } from '../interfaces/labels';

// Entities
import { MoLabels } from '../../db/entity/moLabels';

export default class Labels extends SharedComponents {
    // eslint-disable-next-line
    public async getPublicLabels(attributes: GetLabelInterface): Promise<any> {
        const returnLabels = {};

        try {
            // Fetching previous attempts to login
            const labels = await getConnection().getRepository(MoLabels)
                .find({
                    where: {
                        siteId: attributes.siteId,
                        deleted: false
                    },
                    order: {
                        name: 'ASC'
                    }
                });

            // Converting into flatten object
            for (const label of labels) {
                returnLabels[label.name] = decode(label.output);
            }
        } catch (error) {
            console.error(error);
        }

        return returnLabels;
    }

    public async getLabels(attributes: GetLabelInterface): Promise<Array<LabelsFormInterface>> {
        const returnLabels = [];

        try {
            // Fetching previous attempts to login
            const labels = await getConnection().getRepository(MoLabels)
                .find({
                    where: {
                        siteId: attributes.siteId,
                        deleted: false
                    },
                    order: {
                        name: 'ASC'
                    }
                });

            for (const label of labels) {
                returnLabels.push({
                    id: label.id,
                    name: label.name,
                    output: this.stripLabel(decode(label.output))
                });
            }
        } catch (error) {
            console.error(error);
        }

        return returnLabels;
    }

    public async getLabel(attributes: GetLabelInterface): Promise<LabelsFormInterface> {
        let returnLabel = null;

        try {
            // Fetching previous attempts to login
            const label = await getConnection().getRepository(MoLabels)
                .findOne({
                    siteId: attributes.siteId,
                    deleted: false,
                    id: attributes.id
                });

            if (label) {
                returnLabel = {
                    id: label.id,
                    name: label.name,
                    output: decode(label.output)
                };
            }
        } catch (error) {
            console.error(error);
        }

        return returnLabel;
    }

    public async addLabel(attributes: LabelsFormInterface): Promise<boolean> {
        const formData = await this.checkForm(attributes, 'add');

        if (!formData) {
            return false;
        }

        const newLabel = new MoLabels();

        newLabel.siteId = attributes.siteId;
        newLabel.name = attributes.name;
        newLabel.output = attributes.output;

        try {
            await getConnection().getRepository(MoLabels).save(newLabel);
        } catch (error) {
            console.error(error);

            return false;
        }

        return true;
    }

    public async editLabel(attributes: LabelsFormInterface): Promise<boolean> {
        const formData = await this.checkForm(attributes, 'edit');

        if (!formData) {
            return false;
        }

        try {
            const label = await getConnection().getRepository(MoLabels)
                .findOne({
                    id: attributes.id
                });

            label.name = attributes.name;
            label.output = attributes.output;

            await getConnection().getRepository(MoLabels).save(label);
        } catch (error) {
            console.error(error);

            return false;
        }

        return true;
    }

    public async deleteLabel(id: number): Promise<boolean> {
        try {
            const label = await getConnection().getRepository(MoLabels)
                .findOne({
                    id: id
                });

            label.deleted = true;

            await getConnection().getRepository(MoLabels).save(label);
        } catch (error) {
            console.error(error);

            return false;
        }

        return true;
    }

    private async checkForm(attributes: LabelsFormInterface, type: string): Promise<boolean> {
        if (!attributes.name) {
            this.message += 'Name is empty<br />';
            this.fields.push('name');
        } else if (attributes.name.length > 100) {
            this.message += 'Name is too long<br />';
            this.fields.push('name');
        } else if (attributes.name.match(/-|\s/g)) {
            this.message += 'Name must not have spaces or dashes, please use underscore<br />';
            this.fields.push('name');
        } else if (type === 'add' && await this.checkIfLabelExist(attributes)) {
            this.message += 'Label with this key name is already in use<br />';
            this.fields.push('name');
        } else if (type === 'edit' && await this.checkIfLabelExist(attributes, attributes.id)) {
            this.message += 'Label with this key name is already in use<br />';
            this.fields.push('name');
        }

        if (this.message) {
            return false;
        }

        return true;
    }

    private async checkIfLabelExist(attributes: LabelsFormInterface, id: number = 0): Promise<boolean> {
        const label = await getConnection().getRepository(MoLabels)
            .findOne({
                name: attributes.name,
                siteId: attributes.siteId,
                deleted: false,
                id: Not(id)
            });

        if (label) {
            return true;
        }

        return false;
    }

    private stripLabel(label: string): string {
        label = label.replace(/(<([^>]+)>)/gi, '')
            .replace('&nbsp;', '')
            .trim();

        if (label.length >= 100) {
            label = `${label.substring(0, 100)}...`;
        }

        return label;
    }
}