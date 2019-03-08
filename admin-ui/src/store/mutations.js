// Mutations are always synchronous
export default {
    loggedIn: (state, value) => {
        state.loggedIn = value;
    },
    user: (state, value) => {
        state.user = value;
    }
};