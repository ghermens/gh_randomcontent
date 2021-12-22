.. include:: /Includes.txt


.. _configuration:

Configuration
=============

TypoScript Setup and Constants
------------------------------

Properties of plugin.tx_ghrandomcontent_pi1. You can use the Constant Editor to change these settings.

.. _confval-pages:

pages
~~~~~

:aspect:`Property:`
   pages

:aspect:`Data type:`
   :ref:`page\_id <t3tsref:data-type-page-id>` / :ref:`list <t3tsref:data-type-list>`

:aspect:`Description:`
   IDs of the pages, where the content is stored.

:aspect:`Default:`
   0

.. _confval-count:

count
~~~~~

:aspect:`Property:`
   count

:aspect:`Data type:`
   :ref:`int+ <t3tsref:data-type-intplus>`

:aspect:`Description:`
   Number of content elements to show.

:aspect:`Default:`
   1

.. _confval-honorLanguage:

honorLanguage
~~~~~~~~~~~~~

:aspect:`Property:`
   honorLanguage

:aspect:`Data type:`
   :ref:`boolean <t3tsref:data-type-boolean>`

:aspect:`Description:`
   If :php:`TRUE`, only content elements with the current sys_language_uid are considered.

:aspect:`Default:`
   0

.. _confval-honorColPos:

honorColPos
~~~~~~~~~~~

:aspect:`Property:`
   honorColPos

:aspect:`Data type:`
   :ref:`boolean <t3tsref:data-type-boolean>`

:aspect:`Description:`
   If :php:`TRUE`, only content elements with the same column setting like the plugin itself are considered.

:aspect:`Default:`
   0

.. _confval-defaultColPos:

defaultColPos
~~~~~~~~~~~~~

:aspect:`Property:`
   defaultColPos

:aspect:`Data type:`
   :ref:`int+ <t3tsref:data-type-intplus>`

:aspect:`Description:`
   Default column for honorColPos if plugin is included in the TypoScript Setup.

:aspect:`Default:`
   0

.. _confval-elementWrap:

elementWrap
~~~~~~~~~~~

:aspect:`Property:`
   elementWrap

:aspect:`Data type:`
   :ref:`wrap <t3tsref:data-type-wrap>` / :ref:`stdWrap <t3tsref:stdwrap>`

:aspect:`Description:`
   Wraps each single content element.

:aspect:`Default:`
   |

.. _confval-allWrap:

allWrap
~~~~~~~

:aspect:`Property:`
   allWrap

:aspect:`Data type:`
   :ref:`wrap <t3tsref:data-type-wrap>` / :ref:`stdWrap <t3tsref:stdwrap>`

:aspect:`Description:`
   Wraps the whole output of the plugin.

:aspect:`Default:`
   <div class=”tx-ghrandomcontent-pi1”>|</div>


Examples
--------

You can include the plugin directly in your TypoScript setup:

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
