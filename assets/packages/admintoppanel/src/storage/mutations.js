export default {
    setVisibility: (state, newState) => {
        state.visible = newState;
    },
    setPermissions: (state, newState) => {
      state.permissions = newState;
    },
    setTopBar: (state, newState) => {
      state.topbar = newState;
    },
    setTopBarExtended: (state, newState) => {
      state.topbarextended = newState;
    },
    setQid: (state, newState) => {
      state.qid = newState;
    },
    setGid: (state, newState) => {
      state.gid = newState;
    },
    setType: (state, newState) => {
      state.type = newState;
    },
    setSid: (state, newState) => {
      state.sid = newState;
    },
    setShowSaveButton: (state, newState) => {
        state.showSaveButton = newState;
    },
    setCloseButtonUrl: (state, newState) => {
        state.closeButtonUrl = newState;
    },
};
