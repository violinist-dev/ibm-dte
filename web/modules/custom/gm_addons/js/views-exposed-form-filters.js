window.addEventListener('DOMContentLoaded', function() {
  var filters = document.querySelectorAll('.views-exposed-form');

  if (filters) {
    filters.forEach(function (filter) {
      wrapFilters(filter);
    });
  }

  function wrapFilters(filter) {
    var fieldset = document.createElement('details');
    var label = document.createElement('summary');
    label.textContent = 'Filters';
    var filters = filter.querySelector('.form--inline');
    filter.insertBefore(fieldset, filters);
    fieldset.appendChild(label);
    fieldset.appendChild(filters);
  }
});
