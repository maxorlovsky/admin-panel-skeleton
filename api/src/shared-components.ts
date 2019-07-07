export default class SharedComponents {
    public message: string;
    public fields: Array<string>;

    constructor() {
        this.message = '';
        this.fields = [];
    }

    public getMessage(): string {
        if (!this.message) {
            return 'Unhandled error, no message received';
        }

        return this.message.trim();
    }

    public getFields(): Array<string> | boolean {
        if (!this.fields) {
            return false;
        }

        return this.fields;

        // Deliver back unique names, ignoring duplicates
        // TODO: Reimplement this, to escape duplicated values
        /* 1 return this.filter((this.fields, index, self) => {
            return self.indexOf(value) === index;
        }); */
    }
}