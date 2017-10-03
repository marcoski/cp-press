(function($){
  
  $.CpContactForm = {
    View: {}
  };
  
  $.fn.cpFormFadeOut = function(){
    return $this.each(function(){
      $(this).animate({
        opacity: 0
      }, 'fast', function(){
        $(this).css({'z-index': -100});
      });
    });
  };
  
  $.CpContactForm.View = Backbone.View.extend({
    
    $form: null,
    settings: null,
    
    events: {
      'click .cppress-cf-acceptance': 'toggleSubmit',
      'click .wpcf7-exclusive-checkbox input:checkbox': 'exclusiveCheckbox'
    },
    
    initialize: function(){
      var _that = this;
      this.settings = _cf;
      this.$form = this.$el.find('form');
      this.$form.ajaxForm({
        beforeSubmit: function(arr, $form, options) {
          _that.clearResponseOutput();
          $form.find('[aria-invalid]').attr('aria-invalid', 'false');
          $form.find('img.cppress-ajax-loader').css({ visibility: 'visible' });
          return true;
        },
        beforeSerialize: function($form, options) {
          $form.find('[placeholder].placeheld').each(function(i, n) {
            $(n).val('');
          });
          return true;
        },
        data: { '_cppress-cf-isajaxcall': 1, 'action': 'cppress_cf_ajax', '_cppress_front_ajax': 1 },
        dataType: 'json',
        success: this.success,
        context: this,
        error: function(xhr, status, error, $form) {
          var e = $('<div class="ajax-error"></div>').text(error.message);
          $form.after(e);
        }
      });
      
      this.toggleSubmit();
      this.ajaxLoader();
      
      /** TODO Support datepicker for bootstrap */
    },
    
    success: function(data, status, xhr, $form){
      if(!$.isPlainObject(data) || $.isEmptyObject(data)){
        return;
      }
      
      var $responseOutput = this.$form.find('div.cppress-cf-response-output');
      this.clearResponseOutput();
      
      this.$form.find('.cppress-cf-form-control').removeClass('cppress-cf-not-valid');
      this.$form.removeClass('invalid spam sent failed');
      
      if(data.invalids){
        _.each(data.invalids, function(el, k){
          this.notValidTip(el.into, el.message);
          this.$form.find(el.into).find('.cppress-cf-form-control').addClass('cppress-cf-not-valid');
          this.$form.find(el.into).find('[aria-invalid]').attr('aria-invalid', 'true');
        }, this);
        $responseOutput.addClass('cppress-cf-validation-errors');
        $form.addClass('invalid');
        
        $(data.into).trigger('cppress-cf:invalid');
        
      }else if(1 == data.spam){
        $responseOutput.addClass('cppress-cf-spam-blocked');
        this.$form.addClass('spam');
      }else if(1 == data.mailSent){
        $responseOutput.addClass('cppress-cf-mail-sent-ok');
        this.$form.addClass('sent');
        if(data.onSentOk){
          _.each(data.onSentOk, function(el, i){ eval(el); });
        }
        
        $(data.into).trigger('cppress-cf:mailsent');
      }else{
        $responseOutput.addClass('cppress-cf-mail-sent-ng');
        $form.addClass('failed');
        
        $(data.into).trigger('cppress-cf:mailfailed');
      }
      
      if(data.onSubmit){
        _.each(data.onSubmit, function(el, i){ eval(el); });
      }
      
      $(data.into).trigger('cppress-cf:submit');
      if(1 == data.mailSent){
        this.$form.resetForm();
      }
      
      this.$form.find('[placeholder].placeheld').each(function(i, n){
        $(n).val($n.attr('placeholder'));
      });
      
      $responseOutput.append(data.message).slideDown('fast');
      $responseOutput.attr('role', 'alert');
      
      this.updateScreenReaderResponse(data);
      
    },
    
    updateScreenReaderResponse: function(data){
      this.$form.siblings('.screen-reader-response').html('').attr('role', '');
      if(data.message){
        var $response = this.$form.siblings('.screen-reader-response').first();
        $response.append(data.message);
        
        if(data.invalids){
          var $invalids = $('<ul></ul>');
          _.each(data.invalids, function(el, i){
            if(el.idref){
              var $li = $('<li></li>').append($('<a></a>').attr('href', '#' + el.idref).append(el.message));
            }else{
              var $li = $('<li></li>').append(el.message);
            }
            
            $invalids.append($li);
          }, this);
          
          $response.append($invalids);
        }
        
        $response.attr('role', 'alert').focus();
      }
    },
    
    notValidTip: function(el, message){
      return this.$form.find(el).each(function(){
        var $into = $(this);
        $into.find('span.cppress-cf-not-valid-tip').remove();
        $into.append('<span role="alert" class="cppress-cf-not-valid-tip">' + message + '</span>');
        if($into.is('.use-floating-validation-tip *')){
          $('.cppress-cf-not-valid-tip', $into).mouseover(function(){
             $(this).cpFormFadeOut();
          });
          $(':input', $into).focus(function(){
            $('.cppress-cf-not-valid-tip', $into).not(':hidden').cpFormFadeOut();
          });
        }
      });
    },
    
    clearResponseOutput: function(){
      return this.$form.each(function() {
        $(this).find('div.cppress-cf-response-output')
        .hide()
        .empty()
        .removeClass('cppress-cf-mail-sent-ok cppress-cf-mail-sent-ng cppress-cf-validation-errors cppress-cf-spam-blocked').removeAttr('role');
        $(this).find('span.cppress-cf-not-valid-tip').remove();
        $(this).find('img.cppress-ajax-loader').css({ visibility: 'hidden' });
      });
    },
    
    toggleSubmit: function(){
      return this.$form.each(function(){
        var $form = $(this);
        if(this.tagName.toLowerCase() != 'form'){
          $form = $(this).find('form').first();
        }
        
        if($form.hasClass('cppress-cf-acceptance-as-validation')){
          return;
        }
        
        var $submit = $form.find('input:submit');
        if(!$submit.length){ return; }
        
        var $acceptances = $form.find('input:checkbox.cppress-cf-acceptance');
        if(!$acceptances.length){ return; }
        
        $submit.removeAttr('disabled');
        $acceptances.each(function(i, $el){
          if($el.hasClass('cppress-cd-invert') && $el.is(':checked')
          || $el.hasClass('cppress-cf-invert') && !$el.is(':checked')){
            $submit.attr('disabled', 'disabled');
          }
        });
      });
    },
    
    ajaxLoader: function(){
      var _that = this;
      var $submit = this.$form.find('div.cppress-cf-submit');
      return $submit.each(function(){
        var $loader = $('<img class="cppress-ajax-loader" />').
        attr({src: _that.settings.loaderUri, alt: _that.settings.sending})
        .css('visibility', 'hidden');
        
        $(this).append($loader);
      });
    },
    
    exclusiveCheckbox: function(e){
      var $$ = e.target();
      var name = $$.attr('name');
      this.$form.find('input:checkbox[name="' + name +'"]').not(e.target).porp('checked', false);
    },
    
  });
  
}(jQuery));

(function($){
  
  if($('.cppress-cf').length > 0){
    if($('.cppress-cf > form').attr('action').match(/admin-ajax.php/) !== null){
     $contactForm = new $.CpContactForm.View({
        el: $('.cppress-cf')
     }); 
    }
  }
  
}(jQuery));
