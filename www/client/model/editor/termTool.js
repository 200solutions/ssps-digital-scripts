
window.model.TermTool = class TermTool {

    static get sanitize() {
       return {
           span: {
               class: 'cdx-term'
           }
       };
   }

  static get isInline() {
    return true;
  }

  get state() {
    return this._state;
  }

  set state(state) {
    this._state = state;

    this.button.classList.toggle(this.api.styles.inlineToolButtonActive, state);
  }

  static get title() {
      return 'Termín';
  }

  constructor({api}) {
    this.api = api;
    this.button = null;
    this._state = false;

    this.tag = 'SPAN';
    this.class = 'cdx-term';
  }

  render() {
    this.button = document.createElement('button');
    this.button.type = 'button';

    this.button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="11.485" height="11.484" viewBox="0 0 11.485 11.484"><path id="Path_2" data-name="Path 2" d="M11.485,40.1V33.23A1.23,1.23,0,0,0,10.254,32H1.23A1.23,1.23,0,0,0,0,33.23v9.023a1.23,1.23,0,0,0,1.23,1.23H8.1a1.23,1.23,0,0,0,.87-.36l2.15-2.15A1.23,1.23,0,0,0,11.485,40.1ZM8.2,42.154V40.2h1.951Zm2.051-8.924v5.742H7.588a.615.615,0,0,0-.615.615v2.666H1.23V33.23Z" transform="translate(0 -32)"/></svg>';
    this.button.classList.add(this.api.styles.inlineToolButton);

    return this.button;
  }

  surround(range) {
    if (this.state) {
      this.unwrap(range);
      return;
    }

    this.wrap(range);
  }

  wrap(range) {
    const selectedText = range.extractContents();
    const mark = document.createElement(this.tag);

    mark.classList.add(this.class);
    mark.appendChild(selectedText);
    range.insertNode(mark);

    this.api.selection.expandToTag(mark);
  }

  unwrap(range) {
    const mark = this.api.selection.findParentTag(this.tag, this.class);
    const text = range.extractContents();

    mark.remove();

    range.insertNode(text);
  }


  checkState() {
    const mark = this.api.selection.findParentTag(this.tag);

    this.state = !!mark;
  }
}
