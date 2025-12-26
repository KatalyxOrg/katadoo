/**
 * Scripts publics Katadoo
 *
 * @package Katadoo
 * @since 1.0.0
 */

(function ($) {
    'use strict';

    /**
     * Initialisation au chargement du DOM
     */
    $(document).ready(function () {
        // Chargement initial (optionnel si on utilise la délégation)
        // initForms();
    });

    /**
     * Gestionnaire de soumission délégué pour tous les formulaires Katadoo
     * Supporte les formulaires chargés dynamiquement (Popups Elementor)
     */
    $(document).on('submit', '.katadoo-form-inner', function (e) {
        // Stopper la propagation pour éviter que d'autres scripts (Elementor) n'interfèrent
        e.stopPropagation();
        e.stopImmediatePropagation();

        var $form = $(this);

        // Si c'est déjà en cours de traitement, on ne fait rien
        if ($form.hasClass('processing')) {
            e.preventDefault();
            return;
        }

        e.preventDefault();

        var $button = $form.find('.katadoo-button');
        var $message = $form.find('.katadoo-message');
        var formType = $form.data('type');

        // Validation côté client
        if (!validateForm($form)) {
            return;
        }

        // État de chargement
        $form.addClass('processing');
        $button.prop('disabled', true).addClass('loading');
        $message.hide().removeClass('success error');

        // Si ReCaptcha est activé
        if (katadooPublic.recaptcha && katadooPublic.recaptcha.enabled) {
            if (window.grecaptcha) {
                grecaptcha.ready(function () {
                    grecaptcha.execute(katadooPublic.recaptcha.siteKey, { action: 'submit' }).then(function (token) {
                        submitForm($form, token);
                    });
                });
            } else {
                // Si grecaptcha n'est pas encore là, on attend un peu ou on affiche une erreur
                $form.removeClass('processing');
                $button.prop('disabled', false).removeClass('loading');
                $message.addClass('error').html(katadooPublic.strings.error).show();
            }
        } else {
            submitForm($form);
        }
    });

    /**
     * Soumet le formulaire via AJAX
     * 
     * @param {jQuery} $form Le formulaire jQuery
     * @param {string} recaptchaToken Le jeton ReCaptcha (optionnel)
     */
    function submitForm($form, recaptchaToken) {
        var $button = $form.find('.katadoo-button');
        var $message = $form.find('.katadoo-message');
        var formType = $form.data('type');

        // Préparation des données
        var data = $form.serializeArray();

        if (recaptchaToken) {
            data.push({ name: 'recaptcha_token', value: recaptchaToken });
        }

        // Envoi AJAX
        $.ajax({
            url: katadooPublic.ajaxUrl,
            type: 'POST',
            data: $.param(data),
            success: function (response) {
                if (response.success) {
                    $message.addClass('success').html(response.data.message).show();

                    // Réinitialiser le formulaire en cas de succès
                    if (formType === 'newsletter') {
                        $form[0].reset();
                    } else if (formType === 'helpdesk') {
                        $form[0].reset();
                    }
                } else {
                    $message.addClass('error').html(response.data.message).show();
                }
            },
            error: function (xhr, status, error) {
                var errorMessage = katadooPublic.strings.error;

                if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                    errorMessage = xhr.responseJSON.data.message;
                }

                $message.addClass('error').html(errorMessage).show();
            },
            complete: function () {
                $form.removeClass('processing');
                $button.prop('disabled', false).removeClass('loading');

                // Scroll vers le message
                scrollToElement($message);
            }
        });
    }

    /**
     * Valide un formulaire
     *
     * @param {jQuery} $form Le formulaire jQuery
     * @return {boolean} True si valide
     */
    function validateForm($form) {
        var isValid = true;

        // Supprimer les erreurs précédentes
        $form.find('.katadoo-field-error').remove();
        $form.find('.katadoo-input').removeClass('error');

        // Valider les champs requis
        $form.find('[required]').each(function () {
            var $field = $(this);
            var value = $field.val().trim();

            if (!value) {
                showFieldError($field, katadooPublic.strings.fieldRequired);
                isValid = false;
            }
        });

        // Valider les emails
        $form.find('input[type="email"]').each(function () {
            var $field = $(this);
            var value = $field.val().trim();

            if (value && !isValidEmail(value)) {
                showFieldError($field, katadooPublic.strings.invalidEmail);
                isValid = false;
            }
        });

        return isValid;
    }

    /**
     * Affiche une erreur sur un champ
     *
     * @param {jQuery} $field Le champ jQuery
     * @param {string} message Le message d'erreur
     */
    function showFieldError($field, message) {
        $field.addClass('error');
        $field.after('<span class="katadoo-field-error">' + message + '</span>');
    }

    /**
     * Vérifie si un email est valide
     *
     * @param {string} email L'email à valider
     * @return {boolean} True si valide
     */
    function isValidEmail(email) {
        var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    /**
     * Scroll vers un élément
     *
     * @param {jQuery} $element L'élément jQuery
     */
    function scrollToElement($element) {
        if ($element.length && $element.is(':visible')) {
            $('html, body').animate({
                scrollTop: $element.offset().top - 100
            }, 300);
        }
    }

    /**
     * Efface les erreurs d'un champ lors de la saisie
     */
    $(document).on('input', '.katadoo-input', function () {
        var $field = $(this);
        $field.removeClass('error');
        $field.siblings('.katadoo-field-error').remove();
    });

})(jQuery);
