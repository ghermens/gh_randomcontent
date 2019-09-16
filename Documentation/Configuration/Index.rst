.. include:: ../Includes.txt


.. _configuration:

Configuration
=============

Reference
---------

Properties of plugin.tx_ghrandomcontent_pi1. You can use the Constant Editor to change these settings.

pages
^^^^^

.. container:: table-row

    Property
        pages

    Data type
        int+ / list

    Description
        IDs of the pages, where the content is stored.

    Default
        0


count
^^^^^

.. container:: table-row

    Property
        count

    Data type
        int+

    Description
        Number of content elements to show.

    Default
        1


honorLanguage
^^^^^^^^^^^^^

.. container:: table-row

    Property
        honorLanguage

    Data type
        boolean

    Description
        If set, only content elements with the current sys_language_uid are considered.

    Default
        0


honorColPos
^^^^^^^^^^^

.. container:: table-row

    Property
        honorColPos

    Data type
        boolean

    Description
        If set, only content elements with the same column setting like the plugin itself are considered.

    Default
        0


defaultColPos
^^^^^^^^^^^^^

.. container:: table-row

    Property
        defaultColPos

    Data type
        int+

    Description
        Default column for honorColPos if plugin is included in the TypoScript setup.

    Default
        0


elementWrap
^^^^^^^^^^^

.. container:: table-row

    Property
        elementWrap

    Data type
        wrap / stdWrap

    Description
        Wraps each single content element.

    Default
        |


allWrap
^^^^^^^

.. container:: table-row

    Property
        allWrap

    Data type
        wrap / stdWrap

    Description
        Wraps the whole output of the plugin.

    Default
        <div class=”tx-ghrandomcontent-pi1”>|</div>


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
