<?php

namespace Master\Deal\Services;

class NameService
{
    /**
     * Генерирует имя на основе шаблона, подставляя дату создания и ID.
     *
     * @param string $template Шаблон для генерации имени.
     * @param string $creationDate Дата создания, которую нужно подставить в шаблон.
     * @param int $id ID, который нужно подставить в шаблон.
     * @return string Сгенерированное имя.
     */
    public function generateNameFromTemplate($template, $creationDate, $id) {

        // Заменяем в шаблоне строку {{Когда создан}} на фактическую дату создания
        $template = str_replace('{{Когда создан}}', $creationDate, $template);

        // Заменяем в шаблоне строку {{ID}} на фактический ID
        $template = str_replace('{{ID}}', $id, $template);

        return $template;
    }
}