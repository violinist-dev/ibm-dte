window.addEventListener('DOMContentLoaded', function() {
  const moderationForm = document.querySelector('.entity-moderation-form');

  if (moderationForm) {
    const state = moderationForm.querySelector('#edit-current');
    const label = state.querySelector('label').textContent;
    const textState = state.textContent.replace(label, '').replace(/\s/g, '');

    const editExp = /^node\/\d+\/edit/g;

    const edit = [].slice.call(document.querySelectorAll('.block-local-tasks-block .tabs__tab a')).filter(anchor => anchor.dataset.drupalLinkSystemPath.match(editExp))[0].parentNode;

    if (textState !== 'Draft') {
      edit.style.display = 'none';
    }
  } else {
    const edit = document.querySelectorAll('.path-node .tabs.primary [data-drupal-link-system-path^="node/"][data-drupal-link-system-path$="/edit"], .path-node .edit.dropbutton-action a');

    // Hide all Edit links
    [].forEach.call(edit, item => item.parentNode.style.display = 'none');

    // If content is published (no moderation form) and we're on the `view` tab (active tab points to the node itself) we re-show the edit tab (as that's what allows us to create a new draft from published or archive content)
    const activeTab = document.querySelector('.path-node .tabs.primary a.is-active');

    const viewExp = /^node\/\d+$/g;
    const isView = activeTab && activeTab.dataset && activeTab.dataset.drupalLinkSystemPath.match(viewExp);

    if (isView) {
      [].forEach.call(edit, item => item.parentNode.style.display = 'initial');
    }
  }
});
