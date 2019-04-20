// 3rd party libs
import axios from 'axios';

// Globals functions
import { functions } from '../functions.js';

// Website custom config
import websiteConfig from '../../config/config.json';

// Actions are always asynchronous
export default {
    authorization: async (context, value) => {
        try {
            // Add JWT token as default header
            axios.defaults.headers.common.sessionToken = value.token;

            const axiosEndpoints = [axios.get('/api/me')];

            // In case website need to support multibrands, run mutlisite API call as well
            if (websiteConfig.multiBrands) {
                axiosEndpoints.push(axios.get('/api/multisite'));
            }

            const response = await axios.all(axiosEndpoints);

            // Specifying as loggedIn, to check info in router beforeEach
            mo.loggedIn = true;

            // Changing logged on/out state
            context.commit('loggedIn', true);

            // Saving user data
            context.commit('user', response[0].data);

            // Saving multisite data
            if (websiteConfig.multiBrands) {
                context.commit('saveMultiSite', response[1].data);
            }
        } catch (error) {
            console.error('Error fetching resources: ' + error);

            // Triggering the logout
            mo.app.$store.dispatch('logout');
        }
    },
    logout: async (context) => {
        // Changing logged on/out state
        context.commit('loggedIn', false);

        // Saving user data
        context.commit('user', {});

        // Specifying as loggedOut, to check info in router beforeEach
        mo.loggedIn = false;

        // Remove localStorage token
        functions.storage('remove', 'token');

        try {
            await axios.post(`${mo.apiUrl}/logout`);
        } catch (error) {
            console.error('Error trying to logout user: ' + error);
        } finally {
            // Delete JWT token from default header
            delete (axios.defaults.headers.common.sessionToken);
        }
    },
    fetchMenu: async (context) => {
        try {
            const response = await axios.get(`${mo.apiUrl}/menu`);

            context.commit('saveMenu', response.data);
        } catch (error) {
            console.error('Error, during the process of updating user data, please repeat the process or re-login');
        }
    }
};