<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Club;

/**
 * ClubSearch represents the model behind the search form of `common\models\Club`.
 */
class ClubSearch extends Club
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'anio_fundacion', 'activo', 'created_at', 'updated_at'], 'integer'],
            [['nombre', 'razon_social', 'cuit_cuil', 'zona', 'direccion', 'telefono', 'email', 'presidente', 'estadio', 'logo', 'instagram', 'facebook', 'color_primario', 'color_secundario'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = Club::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'anio_fundacion' => $this->anio_fundacion,
            'activo' => $this->activo,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['like', 'razon_social', $this->razon_social])
            ->andFilterWhere(['like', 'cuit_cuil', $this->cuit_cuil])
            ->andFilterWhere(['like', 'zona', $this->zona])
            ->andFilterWhere(['like', 'direccion', $this->direccion])
            ->andFilterWhere(['like', 'telefono', $this->telefono])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'presidente', $this->presidente])
            ->andFilterWhere(['like', 'estadio', $this->estadio])
            ->andFilterWhere(['like', 'logo', $this->logo])
            ->andFilterWhere(['like', 'instagram', $this->instagram])
            ->andFilterWhere(['like', 'facebook', $this->facebook])
            ->andFilterWhere(['like', 'color_primario', $this->color_primario])
            ->andFilterWhere(['like', 'color_secundario', $this->color_secundario]);

        return $dataProvider;
    }
}
