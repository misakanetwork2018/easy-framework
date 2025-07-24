<?php

namespace EasyFrameworkCore;

class LayoutRender extends Render
{
    /**
     * 给layout用的
     * @return void
     */
    private function renderBody(): void
    {
        include $this->view->getBodyViewPath();
    }
}