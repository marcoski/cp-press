(function($){
  $(document).on('widget.loaded', function(){
    if($('.cp-widget-tag-generator').length > 0){
      var tagGenerator = new $.CpContactForm.View.TagGenerator({
        el: $('.cp-widget-tag-generator')
      });
    }
  });
  
  
}(jQuery));

(function($, _){
  
  $.CpContactForm = {
    View: {}
  };
  
  $.CpContactForm.View.TagGenerator = Backbone.View.extend({
    events: {
      'click .cp-tag-generator-button': "generateTag"
    },
    
    fields: null,
    dialog: null,
    
    initialize: function(){
      this.fields = this.$el.data('fields');
    },
    
    generateTag: function(e){
      var $$ = $(e.target);
      _.each(this.fields, function(el, key){
        if($$.hasClass('cp-button-' + key)){
           this.dialog = new $.CpDialog.contactForm.View();
           this.dialog.setView(this);
           this.dialog.setEl(el, key);
           this.dialog.render();
           this.dialog.on('tag-form-loaded', this.onTagLoaded, this);
        }
      }, this);
      
    },
    
    onTagLoaded: function(){
      var $form = $('#taggenerator_form');
      var $shortCodeInput = $form.find('#cp-shortcode-tag-input');
      var _that = this;
      $form.on('change', function(e){
         var $$ = $(e.target);
         if($$.attr('id') != 'cp-shortcode-tag-input'){
           _that.normalize($$);
           var components = _that.compose($form);
           $shortCodeInput.val(components);
         }
      });
    },
    
    normalize: function($input){
      var val = $input.val();
      if ($input.is('input[name="name"]')) {
        val = val.replace(/[^0-9a-zA-Z:._-]/g, '').replace(/^[^a-zA-Z]+/, '');
      }
      if ($input.is('.numeric')) {
        val = val.replace(/[^0-9.-]/g, '');
      }
  
      if ($input.is('#idattr')) {
        val = val.replace(/[^-0-9a-zA-Z_]/g, '');
      }
  
      if ($input.is('#classattr')) {
        val = $.map(val.split(' '), function(n) {
          return n.replace(/[^-0-9a-zA-Z_]/g, '');
        }).join(' ');
  
        val = $.trim(val.replace(/\s+/g, ' '));
      }
      
      if ($input.is('#filesize')) {
        val = val.replace(/[^0-9kKmMbB]/g, '');
      }
  
      if ($input.is('#filetype')) {
        val = val.replace(/[^0-9a-zA-Z.,|\s]/g, '');
      }
  
      if ($input.is('.date')) {
        if (! val.match(/^\d{4}-\d{2}-\d{2}$/)) { // 'yyyy-mm-dd' ISO 8601 format
          val = '';
        }
      }
  
      if ($input.is(':input[name="values"]')) {
        val = $.trim(val);
      }
  
      $input.val(val);
  
      if ($input.is('#exclusive')) {
       this.exclusiveCheckbox($input);
      }
    },
    
    exclusiveCheckbox: function($cb){
      if ($cb.is(':checked')) {
        $cb.siblings(':checkbox.exclusive').prop('checked', false);
      }
    },
    
    compose: function($form){
      var tagType = $form.find('#cp-tag-type').val();
      var name = $form.find('input[name="name"]').val();
      var options = [];
      
      if ($form.find(':input[name="required"]').is(':checked')) {
        tagType += '*';
      }
      
      $form.find('input.option').not(':checkbox,:radio').each(function(i){
        var val = $(this).val();

        if (! val) {
          return;
        }
  
        if ($(this).hasClass('filetype')) {
          val = val.split(/[,|\s]+/).join('|');
        }
  
        if ('classattr' == $(this).attr('name')) {
          $.each(val.split(' '), function(i, n) { options.push('class:' + n); });
        } else {
          options.push($(this).attr('name') + ':' + val);
        }
      });
      
      $form.find('input:checkbox.option').each(function(i) {
        if ($(this).is(':checked')) {
          options.push($(this).attr('name'));
        }
      });
  
      options = (options.length > 0) ? options.join(' ') : '';
      var value = '';

      if ($form.find(':input[name="values"]').val()) {
        $.each($form.find(':input[name="values"]').val().split(" "), function(i, n) {
          value += ' "' + n.replace(/["]/g, '&quot;') + '"';
        });
      }
      
      var components = [];

      $.each([tagType, name, options, value], function(i, v) {
        v = $.trim(v);
  
        if ('' != v) {
          components.push(v);
        }
      });
  
      components = $.trim(components.join(' '));
      return '[' + components + ']';
    }
    
    
  });
  
}(jQuery, _));

(function($, _){
  $.CpDialog.contactForm = {
    View: {},
    Loader: {}
  },
  
  $.CpDialog.contactForm.Loader = Backbone.View.extend({
    formLoaded: false,
    cpAjax: null,
    el: '#taggenerator_form',

    initialize: function(){
      this.cpAjax = $.fn.cpajax(this);
    },

    render: function(args){
      args || (args={});
      console.log(args);
      args = _.extend({action: 'contact_form_tag'}, args);
      var _that = this;
      this.cpAjax.call(args.action, function(response){
        _that.$el.html(response.data);
        _that.widgetLoaded = true;
        _that.setup();
        _that.trigger('tag-loaded');
      }, {args: JSON.stringify(args)});
    },

    setup: function(){

    },

    attach: function(wrapper){
      wrapper.append(this.$el);
    },

    detach: function(){
      this.$el.detach();
    }
  });
  
  $.CpDialog.contactForm.View = $.CpDialog.grid.View.extend({
    content: 'cppress-dialog-generatetag',
    button: 'cppress-dialog-save-cancel',
    icons: null,
    title: 'Generate Tag',
    element: null,
    hclass: null,
    
    setEl: function(el, key){
      this.element = el;
      this.title += ': ' + el.label;
      this.hclass = key;
    },
    
    render: function(){
      var _that = this;
      this.renderWindow();
      this.$el.find('div.navigation-bar').remove();
      this.$el.find('article').css({"right": 30});
      this.loader = new $.CpDialog.contactForm.Loader();
      this.loader.render({
        element: this.element,
        hclass: this.hclass
      });
      this.loader.attach(this.$el.find('#taggenerator_form'));
      this.loader.on('tag-loaded', function(){
        this.$el.find('article').removeClass("cp-loading cp-panel-loading");
        $.CpField.fn.enable(this.$el.find('#taggenerator_form'));
        this.trigger('tag-form-loaded');
        this.$el.find('#taggenerator_form').trigger('tag.loaded');
      }, this);
      this.$el.find('article').addClass("cp-loading cp-panel-loading");
    },

    save: function(e){
      var content = $('#cp-shortcode-tag-input').val();
      $('.cp-widget-tag-generator textarea').each(function(){
        this.focus();

        if (document.selection) { // IE
          var selection = document.selection.createRange();
          selection.text = content;
        } else if (this.selectionEnd || 0 === this.selectionEnd) {
          var val = $(this).val();
          var end = this.selectionEnd;
          $(this).val(val.substring(0, end) + content + val.substring(end, val.length));
          this.selectionStart = end + content.length;
          this.selectionEnd = end + content.length;
        } else {
          $(this).val($(this).val() + content);
        }
  
        this.focus();
      });
      
      this.trigger('tag:insert');
        
      this.close(e);
    }
  });
  
  
  
}(jQuery, _));
