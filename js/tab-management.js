// This file should come after our common.js so that we already have our global app variable defined

$(async ƒ => {
  app.tabsReference = $('#myTab'); // We need the page to load before we can reference our tabs!
});

app.openTab = (id, title, contentToLoad) => {
  const tabs = app.tabsReference
  if (!tabs.tabExists(id)) {
    tabs.addBSTab(id, title, '');
  }
  
  if (contentToLoad) {
    if (contentToLoad.indexOf('?') > -1) {
      contentToLoad += `&loadedToId=${id}-pane`
    } else {
      contentToLoad += `?loadedToId=${id}-pane`
    }
    $(`#${id}-pane`).load(contentToLoad, function(data) {
      if (app.debug) console.log(contentToLoad + ' (RE)LOADED');
    });
  }
  $(`#${id}`).tab('show');
}

app.closeTab = id => {
  const index = $(`#${id}`).parent().index();
  const parent = $(`#${id}`).parent().parent();
  $(`#${id}`).parent().remove();
  $(`#${id}-pane`).remove();
  $(document).trigger('closed-' + id);
  parent.find('button.nav-link').eq(index - 1).tab('show');
}

app.closeOpenTab = () => {
  const targetId = app.tabsReference.find('button.nav-link:visible.active')[0].id;
  app.closeTab(targetId);
}


$.fn.getActiveTabId = function() {
  return $(this).find('button.active')[0].id;
}

$.fn.tabExists = function(id) {
  return ($('#' + id).length > 0);
}

$.fn.addBSTab = function(id, title, content) {
  const self = $(this);

  ele_with_id = $('#' + id)
  if (ele_with_id.length > 0){
    throw "An element with that ID already exist: '" + id + "'."
  }
  
  ul = $(this);
  container = $($('.tab-content', ul.parent())[0])
  
  li = $('<li />', {
    class: 'nav-item',
    role: 'presentation'
  });
  
  const close = '<a href="#" class="p-0 ms-2 btn-close-tab" data-id="' + id + '" aria-label="Close"><i class="fa-duotone fa-solid fa-xmark"></i></a>';
  button = $('<button />', {
    class: 'nav-link',
    id,
    'data-bs-toggle': 'tab',
    'data-bs-target': '#'+id+'-pane',
    type: 'button',
    role: 'tab',
    html: title + close
  });

  button.on('click', function() {
    const button_element = $(this);
    setTimeout(function() {button_element.tab('show')}, 25);
  });
  
  li . append(button);
  
  div = $('<div />', {
    id: id + '-pane',
    class: 'tab-pane fade',
  });
  
  if (typeof content == 'string'){
    div.html(content);
  } else {
    div.append(content);
  }
  
  ul.append(li);
  container.append(div);
  
  button.tab('show');

  $('.btn-close-tab').off('click').on('click', function (e) {
    e.preventDefault();
    const targetId = '#' + $(this).data('id');
    const index = $(targetId).parent().index();
    $(targetId).parent().remove();
    $(targetId + '-pane').remove();
    $(document).trigger('close-' + $(this).data('id'))
    self.find('button.nav-link').eq(index - 1).tab('show');
  })
  
  return {
    content: div,
    button
  }
}

