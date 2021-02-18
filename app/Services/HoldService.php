<?php

namespace App\Services;

use App\Models\Hold\Hold;

class HoldService
{
    /**
     * Returns the current holds in a format
     * that may be converted to a JSON array.
     *
     * @return array
     */
    public function getHolds(): array
    {
        $data = Hold::with('restrictions', 'navaid', 'deemedSeparatedHolds')->get()->toArray();
        foreach ($data as $key => $hold) {
            foreach ($hold['restrictions'] as $restrictionKey => $restriction) {
                $data[$key]['restrictions'][$restrictionKey] =
                    $data[$key]['restrictions'][$restrictionKey]['restriction'];
            }

            foreach ($hold['deemed_separated_holds'] as $separatedKey => $deemedSeparated) {
                $data[$key]['deemed_separated_holds'][$separatedKey] = $deemedSeparated['id'];
            }

            $data[$key]['fix'] = $data[$key]['navaid']['identifier'];
            unset($data[$key]['navaid_id'], $data[$key]['navaid']);
        }

        return $data;
    }
}
