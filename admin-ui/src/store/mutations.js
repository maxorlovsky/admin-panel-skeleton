// Mutations are always synchronous
export default {
    loggedIn: (state, value) => {
        state.loggedIn = value;
    },
    user: (state, value) => {
        state.user = value;
    },
    drawer: (state) => {
        state.drawer = !state.drawer;
    },
    saveMultiSite: (state, value) => {
        state.multiSite = value;
    },
    saveMultiSiteId: (state, value) => {
        state.multiSiteId = value;
    },
    saveMenu: (state, value) => {
        state.menu = value;
    }
};