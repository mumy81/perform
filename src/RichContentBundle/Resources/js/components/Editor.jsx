import React from 'react';
import css from './editor.scss';
import BlockList from './BlockList';
import Toolbar from './Toolbar';
import 'whatwg-fetch';
import PropTypes from 'prop-types';

class Editor extends React.Component {
  getChildContext() {
    return {store: this.props.store};
  }

  addBlock(e) {
    e.preventDefault();
    this.props.store.dispatch({
      type: 'BLOCK_ADD',
      blockType: 'text'
    });
  }

  render() {
    return (
      <div className={css.editor}>
        <Toolbar add={this.addBlock.bind(this)} />
        <BlockList />
      </div>
    );
  }
}
Editor.childContextTypes = {
  store: PropTypes.object
};

export default Editor;
