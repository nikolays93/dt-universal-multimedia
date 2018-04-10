<?php

namespace NikolayS93\WPAdminForm;

class Active extends Scaffolding
{
    public function set( $active ) {

        $this->active = $active;
    }

    public function get()
    {
        if( ! $this->active )
            $this->active = $this->_active();

        return $this->active;
    }
}
