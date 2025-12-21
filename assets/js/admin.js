/**
 * Scripts d'administration Katadoo
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
        initConnectionTest();
        initMailingListsLoader();
        initHelpdeskTeamsLoader();
        initModuleToggles();
    });

    /**
     * Initialise le test de connexion Odoo
     */
    function initConnectionTest() {
        $('#katadoo-test-connection').on('click', function (e) {
            e.preventDefault();

            var $button = $(this);
            var $result = $('#katadoo-connection-result');

            $button.prop('disabled', true);
            $button.find('.dashicons').addClass('spin');
            $result.hide().removeClass('success error');

            $.ajax({
                url: katadooAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'katadoo_test_connection',
                    nonce: katadooAdmin.nonce
                },
                success: function (response) {
                    if (response.success) {
                        $result.addClass('success').html(response.data.message).show();
                    } else {
                        $result.addClass('error').html(response.data.message).show();
                    }
                },
                error: function () {
                    $result.addClass('error').html(katadooAdmin.strings.error).show();
                },
                complete: function () {
                    $button.prop('disabled', false);
                    $button.find('.dashicons').removeClass('spin');
                }
            });
        });
    }

    /**
     * Initialise le chargement des listes de diffusion
     */
    function initMailingListsLoader() {
        var $select = $('#newsletter_default_list');
        var $refreshBtn = $('#katadoo-refresh-lists');
        var savedValue = $('#newsletter_default_list_value').val();

        if ($select.length === 0) {
            return;
        }

        loadMailingLists();

        $refreshBtn.on('click', function (e) {
            e.preventDefault();
            loadMailingLists();
        });

        function loadMailingLists() {
            $select.prop('disabled', true);
            $refreshBtn.prop('disabled', true);
            $refreshBtn.find('.dashicons').addClass('spin');

            $.ajax({
                url: katadooAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'katadoo_get_mailing_lists',
                    nonce: katadooAdmin.nonce
                },
                success: function (response) {
                    $select.empty();

                    if (response.success && response.data.lists) {
                        $select.append('<option value="0">-- ' + katadooAdmin.strings.selectList + ' --</option>');

                        $.each(response.data.lists, function (i, list) {
                            var selected = parseInt(savedValue) === list.id ? 'selected' : '';
                            $select.append('<option value="' + list.id + '" ' + selected + '>' + list.name + ' (' + list.contact_count + ')</option>');
                        });
                    } else {
                        $select.append('<option value="0">-- Erreur de chargement --</option>');
                    }
                },
                error: function () {
                    $select.empty();
                    $select.append('<option value="0">-- Erreur de connexion --</option>');
                },
                complete: function () {
                    $select.prop('disabled', false);
                    $refreshBtn.prop('disabled', false);
                    $refreshBtn.find('.dashicons').removeClass('spin');
                }
            });
        }
    }

    /**
     * Initialise le chargement des équipes Helpdesk
     */
    function initHelpdeskTeamsLoader() {
        var $select = $('#helpdesk_default_team');
        var $refreshBtn = $('#katadoo-refresh-teams');
        var savedValue = $('#helpdesk_default_team_value').val();

        if ($select.length === 0) {
            return;
        }

        loadHelpdeskTeams();

        $refreshBtn.on('click', function (e) {
            e.preventDefault();
            loadHelpdeskTeams();
        });

        function loadHelpdeskTeams() {
            $select.prop('disabled', true);
            $refreshBtn.prop('disabled', true);
            $refreshBtn.find('.dashicons').addClass('spin');

            $.ajax({
                url: katadooAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'katadoo_get_helpdesk_teams',
                    nonce: katadooAdmin.nonce
                },
                success: function (response) {
                    $select.empty();

                    if (response.success && response.data.teams) {
                        $select.append('<option value="0">-- ' + katadooAdmin.strings.selectTeam + ' --</option>');

                        $.each(response.data.teams, function (i, team) {
                            var selected = parseInt(savedValue) === team.id ? 'selected' : '';
                            $select.append('<option value="' + team.id + '" ' + selected + '>' + team.name + '</option>');
                        });
                    } else {
                        $select.append('<option value="0">-- Erreur de chargement --</option>');
                    }
                },
                error: function () {
                    $select.empty();
                    $select.append('<option value="0">-- Erreur de connexion --</option>');
                },
                complete: function () {
                    $select.prop('disabled', false);
                    $refreshBtn.prop('disabled', false);
                    $refreshBtn.find('.dashicons').removeClass('spin');
                }
            });
        }
    }

    /**
     * Initialise les toggles des modules
     */
    function initModuleToggles() {
        $('.katadoo-toggle input').on('change', function () {
            var $card = $(this).closest('.katadoo-module-card');

            if ($(this).is(':checked')) {
                $card.addClass('active');
            } else {
                $card.removeClass('active');
            }
        });
    }

    /**
     * Animation de rotation pour les icônes
     */
    var style = document.createElement('style');
    style.textContent = '.dashicons.spin { animation: dashicons-spin 1s linear infinite; } @keyframes dashicons-spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }';
    document.head.appendChild(style);

})(jQuery);
