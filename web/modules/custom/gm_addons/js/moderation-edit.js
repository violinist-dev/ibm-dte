window.addEventListener('DOMContentLoaded', function() {
  const moderationForm = document.querySelector('.entity-moderation-form');

  if (moderationForm) {
    const state = moderationForm.querySelector('#edit-current');
    const label = state.querySelector('label').textContent;
    const textState = state.textContent.replace(label, '').replace(/\s/g, '');

    const editExp = /^node\/\d+\/edit/g;

    const edit = [].slice.call(document.querySelectorAll('.block-local-tasks-block .tabs__tab a')).filter(anchor => anchor.dataset.drupalLinkSystemPath.match(editExp))[0].parentNode;

    console.log(textState);
    console.log(edit);

    if (textState !== 'Draft') {
      edit.style.display = 'none';
    }
  }
});
