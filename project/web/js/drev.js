/* =================================================================================== */
/* JQUERY CONTEXT */
/* =================================================================================== */
(function($)
{
    var _doc = $(document);

    $.initExploitation = function()
    {
        $('#btn_exploitation_modifier').click(function(e) {
            $('#btn_exploitation_modifier').addClass("hidden")
            $('#btn_exploitation_annuler').removeClass("hidden")
            $('#row_form_exploitation').removeClass("hidden");
            $('#row_info_exploitation').addClass("hidden");
        });
        if($('#drevDenominationAuto').length){
            if($('#drevDenominationAuto').data("auto")){
                $('#drevDenominationAuto').modal('show');
            }
        }
    }

    $.initPrelevement = function()
    {
        $('.form-chai button').click(function() {
            $('.form-chai button, .form-chai .form-group, .form-chai p').toggleClass('hidden');
        });
    }

    $.initControleExterne = function()
    {
        $('#checkbox_non_conditionneur').on('change', function() {
            if($(this).is(':checked')) {
                $('#bloc-form-control-externe').addClass('opacity-lg');
                $('#bloc-lieu-prelevement').addClass('opacity-lg');
                $('#bloc-form-control-externe input, #bloc-form-control-externe select, #bloc-lieu-prelevement button, #bloc-lieu-prelevement a, #bloc-form-control-externe button, #bloc-form-control-externe a').attr('disabled', 'disabled');
            } else {
                $('#bloc-form-control-externe').removeClass('opacity-lg');
                $('#bloc-lieu-prelevement').removeClass('opacity-lg');
                $('#bloc-form-control-externe input, #bloc-form-control-externe select, #bloc-lieu-prelevement button, #bloc-lieu-prelevement a, #bloc-form-control-externe button, #bloc-form-control-externe a').removeAttr('disabled');
            }
        });
        if($('#checkbox_non_conditionneur').is(':checked')) {
            $('#checkbox_non_conditionneur').change();
        }
    }

    $.initBtnValidation = function()
    {
        $('#btn-validation').click(function() {
            $(this).parents('form').attr('action', $(this).parents('form').attr('action') + '?redirect=validation');
            $(this).parents('form').submit();
            return false;
        });
    }

    $.initFocusAndErrorToRevendicationField = function()
    {
        var field = $('.error_field_to_focused');
        field.focus();
    }

    $.initRevendicationFadeRow = function()
    {

        $('tr.with_superficie').each(function() {
            var children = $(this).find(" td input");
            if (children.length == 2) {
                var fade = true;
                children.each(function() {
                    if ($(this).val() != "") {
                        fade = false;
                    }
                });
                if (fade) {
                    $(this).addClass('with_superficie_fade');
                }
            }

        });
    }

    $.initRevendicationEventsFadeInOut = function() {
        $('tr.with_superficie_fade').each(function() {
            var tr = $(this);
            var children = $(this).find(" td input");
            children.each(function() {
                $(this).focus(function() {
                    tr.removeClass('with_superficie_fade');
                });
                $(this).blur(function() {
                    $.initRevendicationFadeRow();
                });
            });
        });
    }

    $.initEventErrorRevendicationField = function() {
        var field = $('.error_field_to_focused');
        var divError = field.parent().parent();
        field.each(function() {
            $(this).blur(function() {
                if ($(this).val() != "") {
                    divError.removeClass('has-error');
                } else {
                    divError.addClass('has-error');
                }
            });
        });
    }

    $.initRecapEventsAccordion = function() {
        $('#revendication_accordion tr.trAccordion').click(function() {
            var eventId = $(this).attr('data-target');
            var span = $(this).find('small span');

            var ouverts = $('tr td.hiddenRow div.in');
            if (ouverts.length && (eventId == '#' + ouverts.attr('id'))) {
                span.removeClass('glyphicon-chevron-right');
                span.addClass('glyphicon-chevron-down');
                $(eventId).collapse('hide');
            } else {
                $('tr td.hiddenRow div').each(function() {
                    $('#revendication_accordion tr.trAccordion').each(function() {
                        var span_all = $(this).find('small span');
                        span_all.removeClass('glyphicon-chevron-right');
                        span_all.addClass('glyphicon-chevron-down');
                    });
                    $(this).removeClass('in');
                });
                span.removeClass('glyphicon-chevron-down');
                span.addClass('glyphicon-chevron-right');
                $(eventId).collapse('show');

            }
        });
    }

    $.initCalculAuto = function(){

      $(".edit_vci tr.produits").each(function(){
        var produits = $(this);
        produits.find("input.sum_stock_final").change(function(){
          var sum = 0.0;

          produits.find('input.sum_stock_final').each(function(){
            local = $(this).val();
            if(!local){ local=0.0;}else{ local=parseFloat(local); }
            sum=sum+local;
          });
          produits.find("input.stock_final").val(sum.toFixed(2));
        });

      });

        var sommeRevendication = function() {
            $('#table-revendication tbody tr').each(function() {
                var somme = 0;
                $(this).find('.input_sum_value').each(function() {
                    if($(this).val()) {
                        somme += parseFloat($(this).val());
                    } else {
                    	if (parseFloat($(this).text())) {
                        somme += parseFloat($(this).text());
                    	}
                    }
                })
                if (!isNaN(somme)) {
	                if ($(this).find('.input_sum_total').is( "input" )) {
	                	$(this).find('.input_sum_total').val(somme.toFixed(2));
	                } else {
	                    $(this).find('.input_sum_total').text(somme.toFixed(2));
	                }
                }
            });

        }

        $('#table-revendication .input_sum_value').on('change', function() {
            sommeRevendication();
        });

    }

    $.initLots = function() {
        if ($('#form_drev_lots').length == 0)
        {
            return;
        }

        var checkBlocsLot = function() {
            $('#form_drev_lots .bloc-lot').each(function() {
                var saisi = false;
                $(this).find('input, select').each(function() {
                    if(($(this).val() && $(this).attr('data-default-value') != $(this).val()) || $(this).is(":focus")) {
                        saisi = true;
                    }
                });
                if(!saisi) {
                    $(this).addClass('transparence-sm');
                } else {
                    $(this).removeClass('transparence-sm');
                }
            });
        }

        var checkBlocsLotCepages = function() {
            $('#form_drev_lots .ligne_lot_cepage').each(function() {
                var saisi = true;
                $(this).find('input, select').each(function() {
                    if(!$(this).val()) {
                        saisi = false;
                    }
                });
                $(this).find('input, select').each(function() {
                    if($(this).is(":focus")) {
                        saisi = true;
                    }
                });
                if(!saisi) {
                    $(this).addClass('transparence-sm');
                } else {
                    $(this).removeClass('transparence-sm');
                }
            });

            $('#form_drev_lots .modal_lot_cepages').each(function() {
                var total_rest = 100;
                var libelle = "";
                $(this).find('.ligne_lot_cepage .input-float[readonly!=readonly]').each(function() {
                    if(!$(this).val()) {
                        return;
                    }
                    total_rest -= parseFloat($(this).val());
                });
                $(this).find('.ligne_lot_cepage .input-float[readonly=readonly]').val(total_rest.toFixed(2));

                $(this).find('.ligne_lot_cepage').each(function() {
                    var ligne = $(this);
                    var cepage = $(this).find('.form-control').eq(0).val();
                    var pourcentage = parseFloat($(this).find('.form-control').eq(1).val());
                    if(cepage && pourcentage > 0) {
                        if(libelle) {
                            libelle = libelle + ", ";
                        }
                        libelle = libelle + cepage + " ("+ pourcentage +"%)";
                        $(this).removeClass('transparence-sm');
                    } else {
                        $(this).addClass('transparence-sm');
                    }

                    $(this).find('input, select').each(function() {
                        if($(this).is(":focus")) {
                            ligne.removeClass('transparence-sm');
                        }
                    });
                });
                console.log('#lien_'+$(this).attr('id'));
                if(!libelle) {
                    libelle = "Définir le cépage";
                }
                $('#lien_'+$(this).attr('id')).html(libelle);
                console.log(libelle);
            });
        }

        checkBlocsLot();
        checkBlocsLotCepages();
        $('#form_drev_lots input').on('keyup', function() { checkBlocsLot(); checkBlocsLotCepages(); });
        $('#form_drev_lots select').on('change', function() { checkBlocsLot(); checkBlocsLotCepages(); });
        $('#form_drev_lots input').on('focus', function() { checkBlocsLot(); checkBlocsLotCepages(); });
        $('#form_drev_lots select').on('focus', function() { checkBlocsLot(); checkBlocsLotCepages(); });
        $('#form_drev_lots input').on('blur', function() { checkBlocsLot(); checkBlocsLotCepages(); });
        $('#form_drev_lots select').on('blur', function() { checkBlocsLot(); checkBlocsLotCepages(); });

        if(window.location.hash == "#dernier") {
            $('#form_drev_lots .bloc-lot:last input:first').focus();
        } else {
            $('#form_drev_lots .bloc-lot:first input:first').focus();
        }

        $('#form_drev_lots .lot-delete').on('click', function() {
            if(!confirm("Étes vous sûr de vouloir supprimer ce lot ?")) {

                return;
            }

            $(this).parents('.bloc-lot').find('input, select').each(function() {
                $(this).val("");
            });
            $(this).parents('.bloc-lot').find('.select2autocomplete').select2('val', "");
            $(this).parents('.bloc-lot').hide();
        })
    }

    /* =================================================================================== */
    /* FUNCTIONS CALL */
    /* =================================================================================== */
    _doc.ready(function()
    {
        $.initExploitation();
        $.initPrelevement();
        $.initBtnValidation();
        $.initFocusAndErrorToRevendicationField();
        $.initEventErrorRevendicationField();
        $.initCalculAuto();
        $.initRevendicationFadeRow();
        $.initRevendicationEventsFadeInOut();
        $.initControleExterne();
        $.initLots();
        $.initRecapEventsAccordion();
        $.initValidationDeclaration();

    });

})(jQuery);
