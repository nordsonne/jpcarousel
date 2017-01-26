mod.wizards {
    newContentElement.wizardItems {
        plugins {
            elements {
                plugins_tx_jpcarousel_pi1 {
                    icon = EXT:jpcarousel/pi/ce_wiz.gif
                    title = LLL:EXT:jpcarousel/locallang.xml:pi_title
                    description = LLL:EXT:jpcarousel/locallang.xml:pi_plus_wiz_description
                    tt_content_defValues {
                        CType = list
                        list_type = jpcarousel_pi1
                    }
                }
            }
        }
    }
}