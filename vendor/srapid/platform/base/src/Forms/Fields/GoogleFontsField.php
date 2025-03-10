<?php

namespace Srapid\Base\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\SelectType;

class GoogleFontsField extends SelectType
{

    /**
     * {@inheritDoc}
     */
    protected function getTemplate()
    {
        return 'core/base::forms.fields.google-fonts';
    }
}
