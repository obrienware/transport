// import { int, float, decimal, currency } from './formatters.js';
// import { wait, uuidv4 } from './helpers.js';
// import { post, get, queryParams } from './network.js';
// import './type-extensions.js';

const loader = `<div class="loading text-center mt-4"></div>`;
const report_loader = `
  <div class="form-row mt-4">
    <div class="col-3"><div class="mb-2 loading">&nbsp;</div></div>
    <div class="col-3"><div class="mb-2 loading">&nbsp;</div></div>
    <div class="col-3"><div class="mb-2 loading">&nbsp;</div></div>
    <div class="col-3"><div class="mb-2 loading">&nbsp;</div></div>

    <div class="col-3"><div class="mb-2 loading">&nbsp;</div></div>
    <div class="col-3"><div class="mb-2 loading">&nbsp;</div></div>
    <div class="col-3"><div class="mb-2 loading">&nbsp;</div></div>
    <div class="col-3"><div class="mb-2 loading">&nbsp;</div></div>
  </div>
`;

const offset = 220;
const duration = 500;

if ('undefined' !== typeof Dropzone) Dropzone.autoDiscover = false;


const bindPopovers = ƒ => {
  $('.pop').popover({trigger: 'manual' , html: true, animation:false})
    .on('mouseenter', ƒ => {
      const _this = ƒ.currentTarget;
      $(_this).popover('show');
      $('.popover').on('mouseleave', ƒ => $(_this).popover('hide'));
    }).on('mouseleave', ƒ => {
      const _this = ƒ.currentTarget;
      if (!$('.popover:hover').length) $(_this).popover('hide');
    });
};

const reFormat = () => {
  $('[data-bs-toggle="tooltip"]').tooltip();

  $('td.date:not(.short):not(.formatted)').toArray().forEach(item => {
    const value = moment($(item).html(), 'YYYY-MM-DD');
    if (value.isValid()) {
      $(item).addClass('formatted').html(value.format('M/D/YYYY'));
    }
  });

  $('td.datetime:not(.short):not(.formatted), a.datetime:not(.short):not(.formatted)').toArray().forEach(item => {
    const value = moment($(item).html(), 'YYYY-MM-DD HH:mm:ss');
    if (value.isValid()) {
      $(item).addClass('formatted').html(value.format('D/M/YY h:mm a'));
    }
  });

  $('td.time:not(.short):not(.formatted),span.time:not(.short):not(.formatted)').toArray().forEach(item => {
    const value = moment($(item).html(), 'YYYY-MM-DD HH:mm:ss');
    if (value.isValid()) {
      $(item).addClass('formatted').html(value.format('h:mma'));
    }
  });

  $('td.datetime.short:not(.formatted)').toArray().forEach(item => {
    const value = moment($(item).html(), 'YYYY-MM-DD HH:mm:ss');
    if (value.isValid()) {
      $(item).addClass('formatted').html(value.format('M/D h:mma'));
    }
  });
};

export { bindPopovers, reFormat };
$(async ƒ => {
  $('td.date').toArray().forEach(item => {
    const value = moment($(item).html(), 'YYYY-MM-DD');
    if (value.isValid()) {
      $(item).html(value.format('M/D/YYYY'));
    }
  });

  $('td.datetime, a.datetime').toArray().forEach(item => {
    const value = moment($(item).html(), 'YYYY-MM-DD HH:mm:ss');
    if (value.isValid()) {
      $(item).html(value.format('D/M/YY h:mm a'));
    }
  });

  $('td.time,span.time').toArray().forEach(item => {
    const value = moment($(item).html(), 'YYYY-MM-DD HH:mm:ss');
    if (value.isValid()) {
      $(item).html(value.format('h:mma'));
    }
  });

  let pollTimeoutErrors = 0;

  bindPopovers();

  $('.dropdown-menu [data-toggle="dropdown"]').on('click', ƒ => {
    ƒ.preventDefault();
    ƒ.stopPropagation();
    const self = ƒ.currentTarget;

    $('.dropdown-submenu.show').removeClass('show');
    $(self).siblings().toggleClass('show');

    if (!$(self).next().hasClass('show')) {
      $(self).parents('.dropdown-menu').first().find('.show').removeClass('show');
    }

    $(self).parents('.dropdown-menu.show').prev().on('hidden.bs.dropdown', ƒ => {
      $('.dropdown-menu.show').removeClass('show');
    });
  });

  $(document).ajaxStop(reFormat);
});