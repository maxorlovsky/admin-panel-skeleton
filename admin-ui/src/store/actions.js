// 3rd party libs
import firebase from 'firebase';

// Actions are always asynchronous
export default {
    authorization: (context) => {
        if (firebase.auth().currentUser) {
            const user = firebase.auth().currentUser;

            // Changing logged on/out state
            context.commit('loggedIn', true);

            // Saving user data
            context.commit('user', user);
        }
    },
    logout: (context) => {
        firebase.auth().signOut();

        // Changing logged on/out state
        context.commit('loggedIn', false);

        // Saving user data
        context.commit('user', {});
    }
};