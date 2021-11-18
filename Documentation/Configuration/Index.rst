.. include:: /Includes.txt


.. _configuration:

Configuration
=============

Reference
---------

Properties of plugin.tx_ghrandomcontent_pi1. You can use the Constant Editor to change these settings.

.. confval:: pages

   :type: :ref:`page\_id <t3tsref:data-type-page-id>` / :ref:`list <t3tsref:data-type-list>`
   :Default: 0

   IDs of the pages, where the content is stored.

.. confval:: count

   :type: :ref:`int+ <t3tsref:data-type-intplus>`
   :Default: 1

   Number of content elements to show.

.. confval:: honorLanguage

   :type: :ref:`boolean <t3tsref:data-type-boolean>`
   :Default: 0

   If :php:`TRUE`, only content elements with the current sys_language_uid are considered.

.. confval:: honorColPos

   :type: :ref:`boolean <t3tsref:data-type-boolean>`
   :Default: 0

   If :php:`TRUE`, only content elements with the same column setting like the plugin itself are considered.

.. confval:: defaultColPos

   :type: :ref:`int+ <t3tsref:data-type-intplus>`
   :Default: 0

   Default column for honorColPos if plugin is included in the TypoScript setup.

.. confval:: elementWrap

   :type: :ref:`wrap <t3tsref:data-type-wrap>` / :ref:`stdWrap <t3tsref:stdwrap>`
   :Default: |

   Wraps each single content element.

.. confval:: allWrap

   :type: :ref:`wrap <t3tsref:data-type-wrap>` / :ref:`stdWrap <t3tsref:stdwrap>`
   :Default: <div class=”tx-ghrandomcontent-pi1”>|</div>

   Wraps the whole output of the plugin.

Examples
--------

You can include the plugin directly in your TS setup:

.. code-block:: typoscript

    plugin.tx_ghrandomcontent_pi1 {
      pages = 12,15
      count = 1
      honorLanguage = 1
      honorColPos = 1
      defaultColPos = 0
    }
    …
    page.10 < plugin.tx_ghrandomcontent_pi1
    …



Change the output using stdWrap properties:

.. code-block:: typoscript

    plugin.tx_ghrandomcontent_pi1 {
      elementWrap = <li>|</li>
      elementWrap {
        stripHtml = 1
        crop = 100|...|1
      }
      allWrap = <ul>|</ul>
    }
