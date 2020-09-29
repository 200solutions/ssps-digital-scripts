
window.model.MathTool = class MathTool {

    static get sanitize() {
       return {
           samp: {
               class: 'cdx-math katex-math'
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
      return 'Matematika';
  }

  constructor({api}) {
    this.api = api;
    this.button = null;
    this._state = false;

    this.tag = 'SAMP';
    this.class = 'cdx-math';
  }

  render() {
    this.button = document.createElement('button');
    this.button.type = 'button';

    this.button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="12.8" viewBox="0 0 16 12.8"><path d="M7.218,8a6.075,6.075,0,0,1,1.2-3.624.4.4,0,0,0-.054-.553L7.747,3.3a.407.407,0,0,0-.584.056,7.687,7.687,0,0,0,0,9.292.407.407,0,0,0,.584.056l.617-.525a.4.4,0,0,0,.054-.554A6.072,6.072,0,0,1,7.218,8ZM5.6.4A.4.4,0,0,0,5.2,0H4A2.6,2.6,0,0,0,1.4,2.6V4.2H.4a.4.4,0,0,0-.4.4V5.8a.4.4,0,0,0,.4.4h1V9.4a.6.6,0,0,1-.6.6H.4a.4.4,0,0,0-.4.4v1.2a.4.4,0,0,0,.4.4H.8A2.6,2.6,0,0,0,3.4,9.4V6.2h1a.4.4,0,0,0,.4-.4V4.6a.4.4,0,0,0-.4-.4h-1V2.6A.6.6,0,0,1,4,2H5.2a.4.4,0,0,0,.4-.4Zm8.837,2.954a.407.407,0,0,0-.585-.056l-.617.524a.4.4,0,0,0-.054.553,6.07,6.07,0,0,1,0,7.248.4.4,0,0,0,.054.553l.617.525a.407.407,0,0,0,.585-.056,7.687,7.687,0,0,0,0-9.292Zm-1.354,5.8L11.931,8l1.152-1.152a.4.4,0,0,0,0-.566l-.565-.565a.4.4,0,0,0-.566,0L10.8,6.869,9.649,5.717a.4.4,0,0,0-.566,0l-.566.565a.4.4,0,0,0,0,.566L9.669,8,8.517,9.151a.4.4,0,0,0,0,.566l.566.565a.4.4,0,0,0,.566,0L10.8,9.131l1.152,1.152a.4.4,0,0,0,.566,0l.565-.565A.4.4,0,0,0,13.083,9.151Z"/></svg>';

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
    mark.classList.add('katex-math');

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
