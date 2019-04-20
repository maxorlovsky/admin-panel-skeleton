/*
 * Use global mixins sparsely and carefully, because it affects every single Vue instance created, including third party components.
 * In most cases, you should only use it for custom option handling
 */

// Vue
import Vue from 'vue';
import VueNotification from 'vue-notification';

Vue.use(VueNotification);

Vue.mixin({
    methods: {
        /*
         * Mixin to display general messages
         * message(string) = text of the block
         * args:
         * - type(string) = info/error/success/warning
         * - group(string) = general
         */
        displayMessage(message, ...args) {
            const notificationDuration = 10000;

            args = args[0];

            if (!args.type) {
                args.type = 'info';
            }

            if (!args.group) {
                args.group = 'general';
            }

            this.$notify({
                text: message,
                type: args.type,
                title: args.type.charAt(0).toUpperCase() + args.type.slice(1),
                group: args.group,
                duration: notificationDuration
            });
        }
    }
});