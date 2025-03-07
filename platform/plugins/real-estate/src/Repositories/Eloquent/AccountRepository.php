<?php

namespace Botble\RealEstate\Repositories\Eloquent;

use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\DeclareDeclare;

class AccountRepository extends RepositoriesAbstract implements AccountInterface
{
    /**
     * {@inheritDoc}
     */
    public function createUsername($name, $id = null)
    {
        $username = Str::slug($name);
        $index = 1;
        $baseSlug = $username;
        while ($this->model->where('username', $username)->where('id', '!=', $id)->count() > 0) {
            $username = $baseSlug . '-' . $index++;
        }

        if (empty($username)) {
            $username = $baseSlug . '-' . time();
        }

        $this->resetModel();

        return $username;
    }
    public function agents()
    {
        $res = $this->model->where('confirmed_at', '!=', null)->get();
        return $res; 
    }
    function getPolygon($id)
    {
        $res = $this->model->selectRaw('ST_AsGeoJson(agent_area) as poly_coord')->where('id', '=', $id)->get();
        if(env('SWAP_CORD', 'true')) {
            return $this->swapCoordinates($res[0]->poly_coord);
        }

        return $res[0]->poly_coord;
    }

    private function swapCoordinates($geoJson)
    {
        $data = json_decode($geoJson, true);
        if($data) {
            if ($data['type'] === 'Polygon' || $data['type'] === 'MultiPolygon') {
                foreach ($data['coordinates'] as &$polygon) {
                    foreach ($polygon as &$ring) {
                        $ring = array_reverse($ring);
                    }
                }
            }
            return json_encode($data);
        }
        
    }
}
