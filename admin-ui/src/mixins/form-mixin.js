const formMixin = {
    methods: {
        formMixinIsRequired(value) {
            // In case of booleans, if 'false' consider this as satisfying the required criteria
            if (typeof value === 'boolean' && !value) {
                return true;
            }

            return Boolean(value);
        },
        formMixinIsRangeValid(value, min, max) {
            if (max) {
                return (value && (value >= min && value <= max));
            }

            return (value && (value >= min));
        },
        formMixinHandleErrors(error) {
            if (!error.response.data && !error.response.data.fields) {
                return false;
            }

            let errorFields = error.response.data.fields;
            const errorClasses = {};

            // In some cases slim return array as json, we need to convert it
            if (errorFields.constructor && errorFields.constructor !== Array) {
                errorFields = Object.keys(errorFields).map((key) => errorFields[key]);
            }

            // Mark fields with error class
            for (let i = 0; i < errorFields.length; ++i) {
                errorClasses[errorFields[i]] = true;
            }

            return errorClasses;
        }
    }
};

export default formMixin;