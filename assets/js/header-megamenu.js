/**
 * Nalli MegaMenu — Frontend JavaScript
 * Handles: ticker, sticky header, desktop hover mega menu,
 *           tab filtering, mobile drawer, resize, Elementor editor guard.
 * Version: 1.0.0
 */
(function ($) {
  'use strict';

  /**
   * Main widget handler — called once per widget instance by Elementor frontend.
   *
   * @param {jQuery} $scope  The widget root element.
   */
  var NMMHandler = function ($scope, $) {

    // Guard: skip mouse-hover binding only in the actual Elementor EDIT iframe
    // (body has .elementor-editor-active), NOT in preview mode.
    var isEditorMode = document.body.classList.contains('elementor-editor-active');

    var $wrapper   = $scope.find('.nmm-wrapper');
    if ( ! $wrapper.length ) return; // bail if widget HTML isn't ready yet

    var $header    = $wrapper.find('.nmm-header');
    var $annBar    = $wrapper.find('.nmm-ann-bar');
    var $annTrack  = $wrapper.find('.nmm-ann-track');
    var $hamburger = $wrapper.find('.nmm-hamburger');
    var $drawer    = $wrapper.find('.nmm-drawer');
    var $drawerPanel = $wrapper.find('.nmm-drawer-panel');
    var $drawerClose = $wrapper.find('.nmm-drawer-close');
    var $drawerOverlay = $wrapper.find('.nmm-drawer-overlay');

    // Read data attributes.
    var isSticky   = $wrapper.data('sticky') === 1 || $wrapper.data('sticky') === '1';
    var hasShadow  = $wrapper.data('shadow')  === 1 || $wrapper.data('shadow')  === '1';
    var breakpoint = parseInt($wrapper.data('breakpoint') || 1024, 10);

    // -------------------------------------------------------------------------
    // 1. Announcement Bar Ticker
    // -------------------------------------------------------------------------
    (function initTicker() {
      if ($annTrack.length === 0) return;

      // Double the track content for seamless infinite loop.
      var $items = $annTrack.children().clone();
      $annTrack.append($items);

      // Respect speed from data attribute (set by PHP render via inline style).
      var speed = $annBar.data('speed');
      if (speed) {
        $wrapper[0].style.setProperty('--nmm-ann-speed', speed + 'ms');
      }
    })();

    // -------------------------------------------------------------------------
    // 2. Sticky Header
    // -------------------------------------------------------------------------
    var stickyInitialized = false;
    var scrollHandler;

    function initSticky() {
      if (!isSticky || isEditorMode) return;

      var headerHeight = $header.outerHeight() || 70;
      var annHeight    = $annBar.outerHeight() || 0;
      var offset       = annHeight; // stick after ann bar scrolls out

      scrollHandler = function () {
        var scrollY = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollY > offset) {
          $header.addClass('nmm-is-sticky');
          // Push content down to prevent layout jump.
          $wrapper.css('padding-top', headerHeight + 'px');

          if (hasShadow) {
            $header.addClass('nmm-has-shadow');
          }
        } else {
          $header.removeClass('nmm-is-sticky nmm-has-shadow');
          $wrapper.css('padding-top', '');
        }

        // Keep mega panel top in sync with sticky header position.
        updatePanelTop();
      };

      $(window).on('scroll.nmm', scrollHandler);
      stickyInitialized = true;
    }

    function destroySticky() {
      if (!stickyInitialized) return;
      $(window).off('scroll.nmm', scrollHandler);
      $header.removeClass('nmm-is-sticky nmm-has-shadow');
      $wrapper.css('padding-top', '');
      stickyInitialized = false;
    }

    // -------------------------------------------------------------------------
    // 3. Desktop MegaMenu — Hover + Click/Touch behavior
    // -------------------------------------------------------------------------
    var $megaItems = $wrapper.find('.nmm-nav-item.nmm-has-mega');
    var closeTimer = null;

    /**
     * Compute the bottom edge of the header (or announcement bar + header if
     * the header is not yet sticky) and write it to --nmm-panel-top so the
     * fixed-position mega panel always sits flush under the menu bar.
     */
    function updatePanelTop() {
      if ( !$header.length || !$header[0] ) return;
      var rect = $header[0].getBoundingClientRect();
      var panelTop = rect.bottom;
      $wrapper[0].style.setProperty('--nmm-panel-top', panelTop + 'px');
    }

    function openMegaPanel($item) {
      clearTimeout(closeTimer);
      updatePanelTop();
      $megaItems.not($item).each(function () {
        $(this).removeClass('nmm-mega-open');
        $(this).find('> .nmm-nav-link').attr('aria-expanded', 'false');
      });
      $item.addClass('nmm-mega-open');
      $item.find('> .nmm-nav-link').attr('aria-expanded', 'true');
    }

    function closeMegaPanel($item) {
      $item.removeClass('nmm-mega-open');
      $item.find('> .nmm-nav-link').attr('aria-expanded', 'false');
    }

    function closeAllMegaPanels() {
      clearTimeout(closeTimer);
      $megaItems.each(function () {
        closeMegaPanel($(this));
      });
    }

    function initDesktopMega() {
      // Destroy any previously attached events first.
      destroyDesktopMega();

      $megaItems.each(function () {
        var $item  = $(this);
        var $link  = $item.find('> .nmm-nav-link');
        var $panel = $item.find('> .nmm-mega-panel');

        // Hover open on the entire nav-item (link + panel area inside it).
        // The panel is fixed-positioned (outside $item's DOM box) so we also
        // listen on the panel itself.
        $item.on('mouseenter.nmm', function () {
          clearTimeout(closeTimer);
          openMegaPanel($item);
        });

        $item.on('mouseleave.nmm', function () {
          closeTimer = setTimeout(function () {
            closeMegaPanel($item);
          }, 300);
        });

        // The panel is fixed, so it's outside the nav-item DOM flow.
        // Attach enter/leave directly on the panel element.
        $panel.on('mouseenter.nmm', function () {
          clearTimeout(closeTimer);
        });

        $panel.on('mouseleave.nmm', function () {
          closeTimer = setTimeout(function () {
            closeMegaPanel($item);
          }, 300);
        });

        // Click / touch toggle.
        $link.on('click.nmm', function (e) {
          var href = $link.attr('href');
          if (!href || href === '#') {
            e.preventDefault();
          }
          if ($item.hasClass('nmm-mega-open')) {
            closeMegaPanel($item);
          } else {
            openMegaPanel($item);
            e.stopPropagation();
          }
        });
      });

      // Click anywhere outside to close all panels.
      $(document).on('click.nmm-outside', function (e) {
        if (!$(e.target).closest('.nmm-wrapper').length) {
          closeAllMegaPanels();
        }
      });

      // ESC key closes all panels.
      $(document).on('keydown.nmm', function (e) {
        if (e.key === 'Escape') {
          closeAllMegaPanels();
        }
      });
    }

    function destroyDesktopMega() {
      $megaItems.off('mouseenter.nmm mouseleave.nmm');
      $megaItems.find('> .nmm-mega-panel').off('mouseenter.nmm mouseleave.nmm');
      $megaItems.find('> .nmm-nav-link').off('click.nmm');
      $(document).off('click.nmm-outside keydown.nmm');
      clearTimeout(closeTimer);
      closeAllMegaPanels();
    }

    // -------------------------------------------------------------------------
    // 4. MegaMenu Tab Filtering
    // -------------------------------------------------------------------------
    (function initTabFilter() {
      $wrapper.find('.nmm-tab-item').on('click.nmm', function () {
        var $tab    = $(this);
        var $panel  = $tab.closest('.nmm-mega-panel');
        var filter  = $tab.data('filter');

        // Update active tab.
        $panel.find('.nmm-tab-item').removeClass('nmm-tab-active').attr('aria-selected', 'false');
        $tab.addClass('nmm-tab-active').attr('aria-selected', 'true');

        // Filter cards: show matching, hide non-matching.
        // Cards with data-tab="all" are always visible.
        $panel.find('.nmm-cat-card').each(function () {
          var $card    = $(this);
          var cardTab  = $card.data('tab');

          if (!filter || filter === 'all' || cardTab === filter || cardTab === 'all') {
            $card.css('display', '');
          } else {
            $card.css('display', 'none');
          }
        });
      });
    })();

    // -------------------------------------------------------------------------
    // 5. Mobile Drawer — open / close / accordion / focus trap
    // -------------------------------------------------------------------------
    var $lastFocused = null; // for restoring focus on close

    function openDrawer() {
      $lastFocused = $(document.activeElement);
      $wrapper.addClass('nmm-drawer-open');
      $hamburger.attr('aria-expanded', 'true');
      $drawerClose.focus();

      // Prevent body scroll while drawer is open.
      $('body').css('overflow', 'hidden');

      trapFocus($drawerPanel[0]);
    }

    function closeDrawer() {
      $wrapper.removeClass('nmm-drawer-open');
      $hamburger.attr('aria-expanded', 'false');
      $('body').css('overflow', '');
      releaseFocusTrap();

      if ($lastFocused && $lastFocused.length) {
        $lastFocused.focus();
      }
    }

    // Focus trap implementation.
    var focusableSelectors = 'a[href], button:not([disabled]), input, select, textarea, [tabindex]:not([tabindex="-1"])';
    var trapCleanup = null;

    function trapFocus(container) {
      releaseFocusTrap();

      var handler = function (e) {
        if (e.key !== 'Tab') {
          if (e.key === 'Escape') { closeDrawer(); }
          return;
        }

        var focusable = Array.from(container.querySelectorAll(focusableSelectors))
          .filter(function (el) { return !el.closest('[style*="display: none"]') && el.offsetParent !== null; });

        if (focusable.length === 0) return;

        var first = focusable[0];
        var last  = focusable[focusable.length - 1];

        if (e.shiftKey) {
          if (document.activeElement === first) {
            e.preventDefault();
            last.focus();
          }
        } else {
          if (document.activeElement === last) {
            e.preventDefault();
            first.focus();
          }
        }
      };

      document.addEventListener('keydown', handler);
      trapCleanup = function () { document.removeEventListener('keydown', handler); };
    }

    function releaseFocusTrap() {
      if (trapCleanup) { trapCleanup(); trapCleanup = null; }
    }

    // Bind hamburger.
    $hamburger.on('click.nmm', openDrawer);

    // Bind close button & overlay.
    $drawerClose.on('click.nmm', closeDrawer);
    $drawerOverlay.on('click.nmm', closeDrawer);

    // First-level accordion in drawer (nav items with mega menus).
    $wrapper.find('.nmm-drawer-acc-trigger').on('click.nmm', function () {
      var $trigger  = $(this);
      var $body     = $trigger.next('.nmm-drawer-acc-body');
      var $icon     = $trigger.find('.nmm-acc-icon');
      var isOpen    = $body.hasClass('nmm-open');

      $trigger.attr('aria-expanded', isOpen ? 'false' : 'true');
      $body.toggleClass('nmm-open');
      $icon.html( isOpen ? '+' : '&mdash;' );
    });

    // Second-level "Shop By Category" sub-accordion.
    $wrapper.find('.nmm-drawer-sub-trigger').on('click.nmm', function () {
      var $trigger = $(this);
      var $body    = $trigger.next('.nmm-drawer-sub-body');
      var $icon    = $trigger.find('.nmm-acc-icon');
      var isOpen   = $body.hasClass('nmm-open');

      $trigger.attr('aria-expanded', isOpen ? 'false' : 'true');
      $body.toggleClass('nmm-open');
      $icon.html( isOpen ? '+' : '&mdash;' );
    });

    // -------------------------------------------------------------------------
    // 5b. Search Toggle
    // -------------------------------------------------------------------------
    var $searchWrapper = $wrapper.find('.nmm-search-wrapper');
    if ($searchWrapper.length) {
      var $searchBtn = $searchWrapper.find('.nmm-search');
      var $searchDropdown = $searchWrapper.find('.nmm-search-dropdown');
      var $searchInput = $searchDropdown.find('input[type="search"], input[type="text"]').first();

      $searchBtn.on('click.nmm', function (e) {
        e.preventDefault();
        e.stopPropagation(); // prevent document click from firing immediately
        var isOpen = $searchDropdown.is(':visible');
        
        // close mega panels just in case
        closeAllMegaPanels();

        if (isOpen) {
          $searchDropdown.stop().slideUp(200);
          $searchBtn.attr('aria-expanded', 'false');
        } else {
          $searchDropdown.stop().slideDown(200, function() {
            if ($searchInput.length) {
              $searchInput.focus();
            }
          });
          $searchBtn.attr('aria-expanded', 'true');
        }
      });

      $searchDropdown.on('click.nmm', function(e) {
        e.stopPropagation();
      });

      $(document).on('click.nmm-search-outside', function () {
        if ($searchDropdown.is(':visible')) {
          $searchDropdown.stop().slideUp(200);
          $searchBtn.attr('aria-expanded', 'false');
        }
      });
      
      $(document).on('keydown.nmm-search', function (e) {
        if (e.key === 'Escape' && $searchDropdown.is(':visible')) {
          $searchDropdown.stop().slideUp(200);
          $searchBtn.attr('aria-expanded', 'false');
          $searchBtn.focus();
        }
      });

      // AJAX Live Search functionality
      var $resultsContainer = $searchDropdown.find('.nmm-ajax-search-results');
      var $closeBtn = $searchDropdown.find('.nmm-search-close-btn');
      var searchTimer;

      if ($closeBtn.length) {
        $closeBtn.on('click.nmm', function() {
          $searchInput.val('');
          $resultsContainer.empty();
          $searchInput.focus();
        });
      }

      if ($resultsContainer.length) {
        $searchInput.on('input.nmm', function() {
          var query = $(this).val().trim();
          clearTimeout(searchTimer);

          if (query.length < 3) {
            $resultsContainer.empty();
            return;
          }

          $resultsContainer.html('<div class="nmm-search-loading">Searching...</div>');

          searchTimer = setTimeout(function() {
            var ajaxUrl = typeof nmm_ajax !== 'undefined' ? nmm_ajax.ajax_url : '/wp-admin/admin-ajax.php';
            
            var searchType = $searchWrapper.data('search-type') || 'product';

            $.ajax({
              url: ajaxUrl,
              data: { action: 'nmm_ajax_search', q: query, type: searchType },
              dataType: 'json',
              success: function(response) {
                if (response.success && response.data) {
                  if (response.data.length === 0) {
                    $resultsContainer.html('<div class="nmm-search-no-results">No results found.</div>');
                    return;
                  }

                  var html = '<ul class="nmm-search-results-list">';
                  response.data.forEach(function(item) {
                    html += '<li class="nmm-search-result-item">';
                    html += '<a href="' + item.url + '" class="nmm-search-result-link">';
                    if (item.image) {
                      html += '<img src="' + item.image + '" alt="' + item.title + '" />';
                    } else {
                      html += '<div class="nmm-search-no-img"></div>';
                    }
                    html += '<div class="nmm-search-result-info">';
                    html += '<span class="nmm-search-result-title">' + item.title + '</span>';
                    if (item.price) {
                      html += '<span class="nmm-search-result-price">' + item.price + '</span>';
                    }
                    html += '</div></a></li>';
                  });
                  html += '</ul>';
                  
                  // Add a view all link
                  var postTypeParam = searchType === 'category' ? 'product_cat' : 'product';
                  html += '<a href="/?s=' + encodeURIComponent(query) + '&post_type=' + postTypeParam + '" class="nmm-search-view-all">View all results</a>';

                  $resultsContainer.html(html);
                } else {
                  $resultsContainer.html('<div class="nmm-search-error">Error fetching results.</div>');
                }
              },
              error: function() {
                $resultsContainer.html('<div class="nmm-search-error">Failed to connect to server.</div>');
              }
            });
          }, 500); // 500ms debounce
        });
      }
    }

    // -------------------------------------------------------------------------
    // 6. Window Resize — debounced breakpoint re-evaluation
    // -------------------------------------------------------------------------
    var resizeTimer = null;

    function onResize() {
      var width = window.innerWidth;

      if (width > breakpoint) {
        // Desktop mode.
        initDesktopMega();
        initSticky();
        // Close drawer if it was open.
        closeDrawer();
      } else {
        // Mobile mode — disable desktop behaviors.
        destroyDesktopMega();
        destroySticky();
        $header.removeClass('nmm-is-sticky nmm-has-shadow');
        $wrapper.css('padding-top', '');
      }
    }

    $(window).on('resize.nmm', function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(onResize, 100);
    });

    // -------------------------------------------------------------------------
    // 7. Initial Setup
    // -------------------------------------------------------------------------
    onResize(); // run once on init to set correct mode

    // -------------------------------------------------------------------------
    // 8. Force WooCommerce Cart Updates via Official Fragments
    // -------------------------------------------------------------------------
    function updateCartCount() {
      if (!$('span.nmm-cart-count').length) return;
      
      var ajaxUrl = '';
      if (typeof wc_cart_fragments_params !== 'undefined') { 
        ajaxUrl = wc_cart_fragments_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'get_refreshed_fragments' ); 
      } else if (typeof nmm_ajax !== 'undefined' && nmm_ajax.wc_ajax_url) {
        ajaxUrl = nmm_ajax.wc_ajax_url.toString().replace( '%%endpoint%%', 'get_refreshed_fragments' );
      } else {
        ajaxUrl = '/?wc-ajax=get_refreshed_fragments';
      }
      
      $.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: { time: new Date().getTime() },
        success: function(response) {
          if (response && response.fragments && response.fragments['span.nmm-cart-count']) {
            $('span.nmm-cart-count').replaceWith(response.fragments['span.nmm-cart-count']);
          }
        }
      });
    }

    // Always fetch fresh cart count on page load to bypass HTML cache
    updateCartCount();

    // Listen to all WooCommerce events
    $(document.body).on('added_to_cart removed_from_cart updated_cart_totals updated_wc_div wc_fragments_refreshed wc_fragments_loaded', function(event, fragments) {
      if (fragments && fragments['span.nmm-cart-count']) {
        $('span.nmm-cart-count').replaceWith(fragments['span.nmm-cart-count']);
      } else {
        updateCartCount();
      }
    });

  }; // end NMMHandler

  // Register with Elementor frontend.
  $(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction(
      'frontend/element_ready/nalli_header_megamenu.default',
      NMMHandler
    );
  });

  // -------------------------------------------------------------------------
  // 9. Built-in Wishlist Logic (localStorage)
  // -------------------------------------------------------------------------
  $(document).ready(function() {
    var wishlistKey = 'nmm_wishlist_items';
    var wishlist = JSON.parse(localStorage.getItem(wishlistKey) || '[]');

    function updateHeaderWishlistCount() {
      if ($('span.nmm-wishlist-count').length) {
        $('span.nmm-wishlist-count').text(wishlist.length);
      }
    }

    // Initial count update
    updateHeaderWishlistCount();

    // Set initial button states on page load
    $('.nmm-wishlist-btn').each(function() {
      var pid = $(this).data('product-id');
      // Coerce both to strings for safe comparison
      if (wishlist.map(String).indexOf(String(pid)) !== -1) {
        $(this).addClass('in-wishlist');
        $(this).attr('title', 'In Wishlist');
      }
    });

    // Handle Wishlist Button Click
    $(document).on('click', '.nmm-wishlist-btn', function(e) {
      e.preventDefault();
      var $btn = $(this);
      var pid = String($btn.data('product-id'));
      var index = wishlist.map(String).indexOf(pid);

      if (index === -1) {
        // Add to wishlist
        wishlist.push(pid);
        $btn.addClass('in-wishlist');
        $btn.attr('title', 'In Wishlist');
      } else {
        // Remove from wishlist
        wishlist.splice(index, 1);
        $btn.removeClass('in-wishlist');
        $btn.attr('title', 'Add to Wishlist');
      }

      // Save and update UI
      localStorage.setItem(wishlistKey, JSON.stringify(wishlist));
      updateHeaderWishlistCount();
    });

    // Handle Remove Button on Wishlist Page
    $(document).on('click', '.nmm-wishlist-remove', function(e) {
      e.preventDefault();
      var $btn = $(this);
      var pid = String($btn.data('product-id'));
      var index = wishlist.map(String).indexOf(pid);

      if (index !== -1) {
        wishlist.splice(index, 1);
        localStorage.setItem(wishlistKey, JSON.stringify(wishlist));
        updateHeaderWishlistCount();
        
        // Remove the product card from the UI
        $btn.closest('li.product').fadeOut(300, function() {
          $(this).remove();
          
          // Check if wishlist is now empty
          if (wishlist.length === 0) {
            $('#nmm-wishlist-container').html('<div class="nmm-wishlist-empty-state" style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); max-width: 600px; margin: 40px auto;"><svg viewBox="0 0 24 24" width="64" height="64" stroke="#ddd" stroke-width="1.5" fill="none" style="margin-bottom: 20px;"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg><h3 style="font-size: 24px; color: #333; margin-bottom: 10px;">Your wishlist is empty</h3><p style="color: #666; margin-bottom: 24px;">Explore our collections and add your favorite items!</p><a href="/" class="button" style="background: #e02b27; color: #fff; border-radius: 4px; padding: 12px 24px;">Browse Products</a></div>');
          } else {
            // Update the count in the header of the wishlist page
            $('.nmm-wishlist-header span').text(wishlist.length + ' items');
          }
        });
      }
    });

    // -----------------------------------------------------------------------
    // Render Wishlist Page (if [nmm_wishlist_page] is present)
    // -----------------------------------------------------------------------
    var $wishlistContainer = $('#nmm-wishlist-container');
    if ($wishlistContainer.length) {
      if (wishlist.length === 0) {
        $wishlistContainer.html('<p class="cart-empty woocommerce-info">Your wishlist is currently empty.</p>');
      } else {
        var ajaxUrl = '/wp-admin/admin-ajax.php';
        if (typeof wc_add_to_cart_params !== 'undefined') { ajaxUrl = wc_add_to_cart_params.ajax_url; }
        
        $.ajax({
          url: ajaxUrl,
          type: 'POST',
          data: {
            action: 'nmm_render_wishlist',
            product_ids: wishlist
          },
          success: function(response) {
            if (response.success && response.data) {
              $wishlistContainer.html(response.data);
            } else {
              $wishlistContainer.html('<p class="woocommerce-error">Failed to load wishlist.</p>');
            }
          },
          error: function() {
            $wishlistContainer.html('<p class="woocommerce-error">Error loading wishlist. Please try again.</p>');
          }
        });
      }
    }

  });

})(jQuery);
