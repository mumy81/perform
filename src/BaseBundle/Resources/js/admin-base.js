$(function() {
  $('#nav-accordion').dcAccordion({
    eventType: 'click',
    autoClose: false,
    saveState: true,
    disableLink: true,
    speed: 'fast',
    showCount: false,
    autoExpand: true,
    cookie: 'menu-state',
    classExpand: 'dcjq-current-parent'
  });

  function responsiveView() {
    var wSize = $(window).width();
    if (wSize <= 768) {
      $('#container').addClass('sidebar-close');
      $('#sidebar > ul').hide();
    }

    if (wSize > 768) {
      $('#container').removeClass('sidebar-close');
      $('#sidebar > ul').show();
    }
  }
  $(window).on('load', responsiveView);
  $(window).on('resize', responsiveView);

  $('.sidebar-toggler').click(function () {
    var wrapper = $('.wrapper');
    var sidebar = $('#sidebar');
    if (wrapper.hasClass('sidebar-closed')) {
      sidebar.show();
      wrapper.removeClass('sidebar-closed');
    } else {
      sidebar.hide();
      wrapper.addClass('sidebar-closed');
    }
  });

  $('.wrapper form').each(function() {
    window.app.func.fancyForm($(this));
  });

  $('.tooltips').tooltip();

  $('.table-crud tbody tr td').click(function(e) {
    if (e.target === this) {
      var checkbox = $(this).parent().find('.selector');
      checkbox.prop('checked', !checkbox.prop('checked'));
    }
  });

  $('.table-crud thead .batch-selector').change(function(e) {
    $('.table-crud tbody input[type=checkbox].selector').prop('checked', $(this).prop('checked'));
  });
});
