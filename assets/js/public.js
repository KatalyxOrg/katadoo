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
        initForms();
    });

    /**
     * Initialise tous les formulaires Katadoo
     */
    function initForms() {
        $('.katadoo-form-inner').each(function () {
            var $form = $(this);
            initFormSubmission($form);
        });
    }

    /**
     * Initialise la soumission d'un formulaire
     *
     * @param {jQuery} $form Le formulaire jQuery
     */
    function initFormSubmission($form) {
        $form.on('submit', function (e) {
            e.preventDefault();

            var $button = $form.find('.katadoo-button');
            var $message = $form.find('.katadoo-message');
            var formType = $form.data('type');

            // Validation côté client
            if (!validateForm($form)) {
                return;
            }

            // État de chargement
            $button.prop('disabled', true).addClass('loading');
            $message.hide().removeClass('success error');

            // Préparation des données
            var formData = $form.serialize();

            // Envoi AJAX
            $.ajax({
                url: katadooPublic.ajaxUrl,
                type: 'POST',
                data: formData,
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
                    $button.prop('disabled', false).removeClass('loading');

                    // Scroll vers le message
                    scrollToElement($message);
                }
            });
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
