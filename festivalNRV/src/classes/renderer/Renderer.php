<?php

namespace iutnc\nrv\renderer;

interface Renderer {
    const COMPACT = 1;
    const DETAIL = 2;

    public function render(int $selector): string;
}

