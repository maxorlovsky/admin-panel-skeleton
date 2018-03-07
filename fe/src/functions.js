// 3rd party libs
import axios from 'axios';

const functions = {
    storage: (func, key, ...args) => {
        let timeoutSeconds = 1800000;

        if (args[1]) {
            timeoutSeconds = args[1];
        }

        // setItem
        if (func === 'set') {
            // If any parameter is empty, we don't do anything
            if (!func || !key || !args[0]) {
                console.log('false');
                return false;
            }

            let saveData = {
                data: args[0],
                time: (new Date().getTime() + timeoutSeconds),
                version: mo.version
            };

            localStorage.setItem(key, JSON.stringify(saveData));
        } else if (func === 'get') {
            // Check if there is something for specified key
            if (!localStorage.getItem(key)) {
                return false;
            }

            let returnValue = JSON.parse(localStorage.getItem(key));

            if (
                // If older than 30 min
                (returnValue.time <= new Date().getTime()) ||
                // Or if version is now different, ignoring session token
                (returnValue.version !== mo.version && key !== 'token')
               ) {
                // Cleanup
                functions.storage('remove', key);
                return false;
            }

            return returnValue.data;
        } else if (func === 'remove') {
            localStorage.removeItem(key);
        } else {
            return false;
        }

        return true;
    },
    storageCacheBuster: () => {
        const storagesKeys = ['structure-data', 'structure-user-data'];

        // If version was bumped, we might still use outdated localStorage data, doing full cleanup
        if (localStorage.getItem('version') !== mo.version) {
            for (let value of storagesKeys) {
                localStorage.removeItem(value);
            }
            // Saving version to not cleaup everything again until the next bump
            localStorage.setItem('version', mo.version);
        }

        return true;
    },
    checkUserAuth: () => {
        const token = functions.storage('get', 'token');

        if (token.sessionToken) {
            mo.loggedIn = true;
            axios.defaults.headers.common.sessionToken = token.sessionToken;
        }

        return mo.loggedIn;
    },
    getEnv: () => {
        let env = functions.storage('get', 'env');

        if (env === 'dev' || location.host.indexOf('dev') === 0) {
            env = 'dev';
        } else if (env === 'test' || location.host.indexOf('test') === 0 || env === 'qa' || location.host.indexOf('qa') === 0) {
            env = 'test';
        } else {
            env = 'prod';
        }

        return env;
    }
};

export { functions };