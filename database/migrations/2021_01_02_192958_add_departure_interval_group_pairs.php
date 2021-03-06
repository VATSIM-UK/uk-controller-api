<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDepartureIntervalGroupPairs extends Migration
{
    /**
     * Pair format:
     *
     * 0 - First group name
     * 1 - Second group name
     * 2 - Interval in seconds when going First group -> second group
     */
    const PAIRS = [
        [
            'EGBB_SID',
            'EGBB_SID',
            120
        ],
        [
            'EGCC_EKLAD_KUXEM_23',
            'EGCC_EKLAD_KUXEM_23',
            120,
        ],
        [
            'EGCC_EKLAD_KUXEM_23',
            'EGCC_LISTO_23',
            60,
        ],
        [
            'EGCC_EKLAD_KUXEM_23',
            'EGCC_MONTY_23',
            120,
        ],
        [
            'EGCC_EKLAD_KUXEM_23',
            'EGCC_SANBA_23',
            120,
        ],
        [
            'EGCC_EKLAD_KUXEM_23',
            'EGCC_NORTH_EAST_23',
            120,
        ],
        [
            'EGCC_LISTO_23',
            'EGCC_LISTO_23',
            120,
        ],
        [
            'EGCC_LISTO_23',
            'EGCC_EKLAD_KUXEM_23',
            60,
        ],
        [
            'EGCC_LISTO_23',
            'EGCC_MONTY_23',
            60,
        ],
        [
            'EGCC_LISTO_23',
            'EGCC_NORTH_EAST_23',
            120,
        ],
        [
            'EGCC_MONTY_23',
            'EGCC_EKLAD_KUXEM_23',
            120,
        ],
        [
            'EGCC_MONTY_23',
            'EGCC_LISTO_23',
            60,
        ],
        [
            'EGCC_MONTY_23',
            'EGCC_MONTY_23',
            120,
        ],
        [
            'EGCC_MONTY_23',
            'EGCC_NORTH_EAST_23',
            120,
        ],
        [
            'EGCC_MONTY_23',
            'EGCC_SANBA_23',
            120,
        ],
        [
            'EGCC_NORTH_EAST_23',
            'EGCC_EKLAD_KUXEM_23',
            60,
        ],
        [
            'EGCC_NORTH_EAST_23',
            'EGCC_LISTO_23',
            60,
        ],
        [
            'EGCC_NORTH_EAST_23',
            'EGCC_MONTY_23',
            60,
        ],
        [
            'EGCC_NORTH_EAST_23',
            'EGCC_NORTH_EAST_23',
            120,
        ],
        [
            'EGCC_NORTH_EAST_23',
            'EGCC_SANBA_23',
            60,
        ],
        [
            'EGCC_SANBA_23',
            'EGCC_EKLAD_KUXEM_23',
            120,
        ],
        [
            'EGCC_SANBA_23',
            'EGCC_MONTY_23',
            60,
        ],
        [
            'EGCC_SANBA_23',
            'EGCC_NORTH_EAST_23',
            60,
        ],
        [
            'EGCC_SANBA_23',
            'EGCC_SANBA_23',
            120,
        ],
        [
            'EGCC_ASMIM_05',
            'EGCC_ASMIM_05',
            120,
        ],
        [
            'EGCC_ASMIM_05',
            'EGCC_DESIG_05',
            60,
        ],
        [
            'EGCC_ASMIM_05',
            'EGCC_POL_05',
            60,
        ],
        [
            'EGCC_ASMIM_05',
            'EGCC_LISTO_05',
            60,
        ],
        [
            'EGCC_ASMIM_05',
            'EGCC_MONTY_05',
            120,
        ],
        [
            'EGCC_DESIG_05',
            'EGCC_ASMIM_05',
            60,
        ],
        [
            'EGCC_DESIG_05',
            'EGCC_DESIG_05',
            120,
        ],
        [
            'EGCC_DESIG_05',
            'EGCC_LISTO_05',
            60,
        ],
        [
            'EGCC_DESIG_05',
            'EGCC_MONTY_05',
            120,
        ],
        [
            'EGCC_DESIG_05',
            'EGCC_POL_05',
            120,
        ],
        [
            'EGCC_LISTO_05',
            'EGCC_ASMIM_05',
            60,
        ],
        [
            'EGCC_LISTO_05',
            'EGCC_DESIG_05',
            60,
        ],
        [
            'EGCC_LISTO_05',
            'EGCC_LISTO_05',
            120,
        ],
        [
            'EGCC_LISTO_05',
            'EGCC_MONTY_05',
            60,
        ],
        [
            'EGCC_LISTO_05',
            'EGCC_POL_05',
            60,
        ],
        [
            'EGCC_MONTY_05',
            'EGCC_ASMIM_05',
            120,
        ],
        [
            'EGCC_MONTY_05',
            'EGCC_DESIG_05',
            60,
        ],
        [
            'EGCC_MONTY_05',
            'EGCC_LISTO_05',
            60,
        ],
        [
            'EGCC_MONTY_05',
            'EGCC_MONTY_05',
            120,
        ],
        [
            'EGCC_MONTY_05',
            'EGCC_POL_05',
            60,
        ],
        [
            'EGCC_POL_05',
            'EGCC_ASMIM_05',
            60,
        ],
        [
            'EGCC_POL_05',
            'EGCC_DESIG_05',
            120,
        ],
        [
            'EGCC_POL_05',
            'EGCC_LISTO_05',
            60,
        ],
        [
            'EGCC_POL_05',
            'EGCC_MONTY_05',
            60,
        ],
        [
            'EGCC_POL_05',
            'EGCC_POL_05',
            60,
        ],
        [
            'EGCN_ROGAG_02',
            'EGCN_ROGAG_02',
            120,
        ],
        [
            'EGCN_UPTON_02',
            'EGCN_UPTON_02',
            180,
        ],
        [
            'EGCN_ROGAG_02',
            'EGCN_UPTON_02',
            60,
        ],
        [
            'EGCN_UPTON_02',
            'EGCN_ROGAG_02',
            60,
        ],
        [
            'EGCN_UPTON_20',
            'EGCN_UPTON_20',
            180,
        ],
        [
            'EGCN_UPTON_20',
            'EGCN_ROGAG_20',
            120,
        ],
        [
            'EGCN_ROGAG_20',
            'EGCN_ROGAG_20',
            120,
        ],
        [
            'EGCN_ROGAG_20',
            'EGCN_UPTON_20',
            120,
        ],
        [
            'EGFF_SID',
            'EGFF_SID',
            120,
        ],
        [
            'EGGD_SID',
            'EGGD_SID',
            120,
        ],
        [
            'EGGW_CPT_26',
            'EGGW_CPT_26',
            120,
        ],
        [
            'EGGW_CPT_26',
            'EGGW_DET_MATCH_26',
            60,
        ],
        [
            'EGGW_CPT_26',
            'EGGW_OLNEY_26',
            120,
        ],
        [
            'EGGW_DET_MATCH_26',
            'EGGW_CPT_26',
            120,
        ],
        [
            'EGGW_DET_MATCH_26',
            'EGGW_DET_MATCH_26',
            120,
        ],
        [
            'EGGW_DET_MATCH_26',
            'EGGW_OLNEY_26',
            120,
        ],
        [
            'EGGW_OLNEY_26',
            'EGGW_CPT_26',
            120,
        ],
        [
            'EGGW_OLNEY_26',
            'EGGW_DET_MATCH_26',
            60,
        ],
        [
            'EGGW_OLNEY_26',
            'EGGW_OLNEY_26',
            120,
        ],
        [
            'EGGW_CPT_08',
            'EGGW_CPT_08',
            240,
        ],
        [
            'EGGW_CPT_08',
            'EGGW_DET_MATCH_08',
            240,
        ],
        [
            'EGGW_CPT_08',
            'EGGW_OLNEY_08',
            240,
        ],
        [
            'EGGW_DET_MATCH_08',
            'EGGW_CPT_08',
            60,
        ],
        [
            'EGGW_DET_MATCH_08',
            'EGGW_DET_MATCH_08',
            120,
        ],
        [
            'EGGW_DET_MATCH_08',
            'EGGW_OLNEY_08',
            120,
        ],
        [
            'EGGW_OLNEY_08',
            'EGGW_CPT_08',
            60,
        ],
        [
            'EGGW_OLNEY_08',
            'EGGW_DET_MATCH_08',
            60,
        ],
        [
            'EGGW_OLNEY_08',
            'EGGW_OLNEY_08',
            120,
        ],
        [
            'EGJB_SID',
            'EGJB_SID',
            120,
        ],
        [
            'EGJJ_SID',
            'EGJJ_SID',
            120,
        ],
        [
            'EGKK_EAST_26',
            'EGKK_EAST_26',
            120,
        ],
        [
            'EGKK_EAST_26',
            'EGKK_BIG_26',
            120,
        ],
        [
            'EGKK_EAST_26',
            'EGKK_WEST_26',
            60,
        ],
        [
            'EGKK_EAST_26',
            'EGKK_SFD_26',
            60,
        ],
        [
            'EGKK_EAST_26',
            'EGKK_RELIEF_26',
            60,
        ],
        [
            'EGKK_BIG_26',
            'EGKK_EAST_26',
            180,
        ],
        [
            'EGKK_BIG_26',
            'EGKK_BIG_26',
            120,
        ],
        [
            'EGKK_BIG_26',
            'EGKK_WEST_26',
            60,
        ],
        [
            'EGKK_BIG_26',
            'EGKK_SFD_26',
            60,
        ],
        [
            'EGKK_BIG_26',
            'EGKK_RELIEF_26',
            60,
        ],
        [
            'EGKK_WEST_26',
            'EGKK_EAST_26',
            60,
        ],
        [
            'EGKK_WEST_26',
            'EGKK_BIG_26',
            60,
        ],
        [
            'EGKK_WEST_26',
            'EGKK_WEST_26',
            120,
        ],
        [
            'EGKK_WEST_26',
            'EGKK_SFD_26',
            120,
        ],
        [
            'EGKK_WEST_26',
            'EGKK_RELIEF_26',
            60,
        ],
        [
            'EGKK_SFD_26',
            'EGKK_EAST_26',
            60,
        ],
        [
            'EGKK_SFD_26',
            'EGKK_BIG_26',
            60,
        ],
        [
            'EGKK_SFD_26',
            'EGKK_WEST_26',
            120,
        ],
        [
            'EGKK_SFD_26',
            'EGKK_SFD_26',
            120,
        ],
        [
            'EGKK_SFD_26',
            'EGKK_RELIEF_26',
            120,
        ],
        [
            'EGKK_RELIEF_26',
            'EGKK_EAST_26',
            60,
        ],
        [
            'EGKK_RELIEF_26',
            'EGKK_BIG_26',
            60,
        ],
        [
            'EGKK_RELIEF_26',
            'EGKK_WEST_26',
            60,
        ],
        [
            'EGKK_RELIEF_26',
            'EGKK_SFD_26',
            120,
        ],
        [
            'EGKK_RELIEF_26',
            'EGKK_RELIEF_26',
            120,
        ],
        [
            'EGKK_LAM_08',
            'EGKK_LAM_08',
            120,
        ],
        [
            'EGKK_LAM_08',
            'EGKK_EAST_08',
            120,
        ],
        [
            'EGKK_LAM_08',
            'EGKK_BIG_08',
            120,
        ],
        [
            'EGKK_LAM_08',
            'EGKK_WEST_08',
            120,
        ],
        [
            'EGKK_LAM_08',
            'EGKK_SFD_08',
            120,
        ],
        [
            'EGKK_EAST_08',
            'EGKK_LAM_08',
            120,
        ],
        [
            'EGKK_EAST_08',
            'EGKK_EAST_08',
            120,
        ],
        [
            'EGKK_EAST_08',
            'EGKK_BIG_08',
            120,
        ],
        [
            'EGKK_EAST_08',
            'EGKK_WEST_08',
            60,
        ],
        [
            'EGKK_EAST_08',
            'EGKK_SFD_08',
            60,
        ],
        [
            'EGKK_BIG_08',
            'EGKK_LAM_08',
            180,
        ],
        [
            'EGKK_BIG_08',
            'EGKK_EAST_08',
            180,
        ],
        [
            'EGKK_BIG_08',
            'EGKK_BIG_08',
            120,
        ],
        [
            'EGKK_BIG_08',
            'EGKK_WEST_08',
            60,
        ],
        [
            'EGKK_BIG_08',
            'EGKK_SFD_08',
            60,
        ],
        [
            'EGKK_WEST_08',
            'EGKK_LAM_08',
            120,
        ],
        [
            'EGKK_WEST_08',
            'EGKK_EAST_08',
            60,
        ],
        [
            'EGKK_WEST_08',
            'EGKK_BIG_08',
            60,
        ],
        [
            'EGKK_WEST_08',
            'EGKK_WEST_08',
            120,
        ],
        [
            'EGKK_WEST_08',
            'EGKK_SFD_08',
            60,
        ],
        [
            'EGKK_SFD_08',
            'EGKK_LAM_08',
            60,
        ],
        [
            'EGKK_SFD_08',
            'EGKK_EAST_08',
            60,
        ],
        [
            'EGKK_SFD_08',
            'EGKK_BIG_08',
            60,
        ],
        [
            'EGKK_SFD_08',
            'EGKK_WEST_08',
            60,
        ],
        [
            'EGKK_SFD_08',
            'EGKK_SFD_08',
            60,
        ],
        [
            'EGLC_SID_NORTH_WEST',
            'EGLC_SID_NORTH_WEST',
            120,
        ],
        [
            'EGLC_SID_SOUTH_EAST',
            'EGLC_SID_SOUTH_EAST',
            120,
        ],
        [
            'EGLC_SID_NORTH_WEST',
            'EGLC_SID_SOUTH_EAST',
            120,
        ],
        [
            'EGLC_SID_SOUTH_EAST',
            'EGLC_SID_NORTH_WEST',
            120,
        ],
        [
            'EGLL_NORTH_27',
            'EGLL_NORTH_27',
            120,
        ],
        [
            'EGLL_NORTH_27',
            'EGLL_WEST_27L',
            120,
        ],
        [
            'EGLL_NORTH_27',
            'EGLL_WEST_27R',
            60,
        ],
        [
            'EGLL_NORTH_27',
            'EGLL_MAXIT_27',
            60,
        ],
        [
            'EGLL_NORTH_27',
            'EGLL_DET_27',
            60,
        ],
        [
            'EGLL_WEST_27L',
            'EGLL_NORTH_27',
            120,
        ],
        [
            'EGLL_WEST_27R',
            'EGLL_NORTH_27',
            60,
        ],
        [
            'EGLL_WEST_27R',
            'EGLL_WEST_27R',
            120,
        ],
        [
            'EGLL_WEST_27L',
            'EGLL_WEST_27L',
            120,
        ],
        [
            'EGLL_WEST_27R',
            'EGLL_MAXIT_27',
            120,
        ],
        [
            'EGLL_WEST_27L',
            'EGLL_MAXIT_27',
            120,
        ],
        [
            'EGLL_WEST_27R',
            'EGLL_DET_27',
            120,
        ],
        [
            'EGLL_WEST_27L',
            'EGLL_DET_27',
            120,
        ],
        [
            'EGLL_MAXIT_27',
            'EGLL_NORTH_27',
            60,
        ],
        [
            'EGLL_MAXIT_27',
            'EGLL_WEST_27L',
            120,
        ],
        [
            'EGLL_MAXIT_27',
            'EGLL_WEST_27R',
            120,
        ],
        [
            'EGLL_MAXIT_27',
            'EGLL_MAXIT_27',
            120,
        ],
        [
            'EGLL_MAXIT_27',
            'EGLL_DET_27',
            120,
        ],
        [
            'EGLL_DET_27',
            'EGLL_NORTH_27',
            60,
        ],
        [
            'EGLL_DET_27',
            'EGLL_WEST_27L',
            60,
        ],
        [
            'EGLL_DET_27',
            'EGLL_WEST_27R',
            60,
        ],
        [
            'EGLL_DET_27',
            'EGLL_MAXIT_27',
            120,
        ],
        [
            'EGLL_DET_27',
            'EGLL_DET_27',
            180,
        ],
        [
            'EGLL_NORTH_09',
            'EGLL_NORTH_09',
            120,
        ],
        [
            'EGLL_NORTH_09',
            'EGLL_CPT_09',
            60,
        ],
        [
            'EGLL_NORTH_09',
            'EGLL_GASGU_09',
            60,
        ],
        [
            'EGLL_NORTH_09',
            'EGLL_MODMI_09',
            60,
        ],
        [
            'EGLL_NORTH_09',
            'EGLL_DET_09L',
            60,
        ],
        [
            'EGLL_NORTH_09',
            'EGLL_DET_09R',
            60,
        ],
        [
            'EGLL_CPT_09',
            'EGLL_NORTH_09',
            60,
        ],
        [
            'EGLL_CPT_09',
            'EGLL_CPT_09',
            120,
        ],
        [
            'EGLL_CPT_09',
            'EGLL_GASGU_09',
            120,
        ],
        [
            'EGLL_CPT_09',
            'EGLL_MODMI_09',
            120,
        ],
        [
            'EGLL_CPT_09',
            'EGLL_DET_09L',
            120,
        ],
        [
            'EGLL_CPT_09',
            'EGLL_DET_09R',
            120,
        ],
        [
            'EGLL_GASGU_09',
            'EGLL_NORTH_09',
            60,
        ],
        [
            'EGLL_GASGU_09',
            'EGLL_CPT_09',
            180,
        ],
        [
            'EGLL_GASGU_09',
            'EGLL_GASGU_09',
            120,
        ],
        [
            'EGLL_GASGU_09',
            'EGLL_MODMI_09',
            180,
        ],
        [
            'EGLL_GASGU_09',
            'EGLL_DET_09L',
            120,
        ],
        [
            'EGLL_GASGU_09',
            'EGLL_DET_09R',
            120,
        ],
        [
            'EGLL_MODMI_09',
            'EGLL_NORTH_09',
            60,
        ],
        [
            'EGLL_MODMI_09',
            'EGLL_CPT_09',
            120,
        ],
        [
            'EGLL_MODMI_09',
            'EGLL_GASGU_09',
            120,
        ],
        [
            'EGLL_MODMI_09',
            'EGLL_MODMI_09',
            120,
        ],
        [
            'EGLL_MODMI_09',
            'EGLL_DET_09L',
            120,
        ],
        [
            'EGLL_MODMI_09',
            'EGLL_DET_09R',
            120,
        ],
        [
            'EGLL_DET_09L',
            'EGLL_NORTH_09',
            60,
        ],
        [
            'EGLL_DET_09R',
            'EGLL_NORTH_09',
            60,
        ],
        [
            'EGLL_DET_09L',
            'EGLL_CPT_09',
            120,
        ],
        [
            'EGLL_DET_09R',
            'EGLL_CPT_09',
            120,
        ],
        [
            'EGLL_DET_09L',
            'EGLL_GASGU_09',
            120,
        ],
        [
            'EGLL_DET_09R',
            'EGLL_GASGU_09',
            120,
        ],
        [
            'EGLL_DET_09L',
            'EGLL_MODMI_09',
            120,
        ],
        [
            'EGLL_DET_09R',
            'EGLL_MODMI_09',
            120,
        ],
        [
            'EGLL_DET_09L',
            'EGLL_DET_09L',
            120,
        ],
        [
            'EGLL_DET_09R',
            'EGLL_DET_09R',
            120,
        ],
        [
            'EGLF_SID',
            'EGLF_SID',
            120
        ],
        [
            'EGNM_DOPEK_LAMIX',
            'EGNM_DOPEK_LAMIX',
            120,
        ],
        [
            'EGNM_DOPEK_LAMIX',
            'EGNM_SID_NELSA',
            120,
        ],
        [
            'EGNM_SID_NELSA',
            'EGNM_DOPEK_LAMIX',
            60,
        ],
        [
            'EGNM_SID_NELSA',
            'EGNM_SID_NELSA',
            120,
        ],
        [
            'EGNM_DOPEK_LAMIX',
            'EGNM_SID_POL',
            60,
        ],
        [
            'EGNM_SID_POL',
            'EGNM_SID_POL',
            240,
        ],
        [
            'EGNM_SID_POL',
            'EGNM_DOPEK_LAMIX',
            240,
        ],
        [
            'EGNX_TNT_POL',
            'EGNX_TNT_POL',
            120,
        ],
        [
            'EGNX_TNT_POL',
            'EGNX_DTY_BPK',
            60,
        ],
        [
            'EGNX_DTY_BPK',
            'EGNX_DTY_BPK',
            120,
        ],
        [
            'EGNX_DTY_BPK',
            'EGNX_TNT_POL',
            60,
        ],
        [
            'EGPF_NORBO_LUSIV_TLA_TRN',
            'EGPF_NORBO_LUSIV_TLA_TRN',
            120,
        ],
        [
            'EGPF_NORBO_LUSIV_TLA_TRN',
            'EGPF_CLYDE_PTH',
            60,
        ],
        [
            'EGPF_NORBO_LUSIV_TLA_TRN',
            'EGPF_FOYLE',
            60,
        ],
        [
            'EGPF_NORBO_LUSIV_TLA_TRN',
            'EGPF_LOMON',
            60,
        ],
        [
            'EGPF_NORBO_LUSIV_TLA_TRN',
            'EGPF_ROBBO',
            120,
        ],
        [
            'EGPF_FOYLE',
            'EGPF_NORBO_LUSIV_TLA_TRN',
            60,
        ],
        [
            'EGPF_FOYLE',
            'EGPF_FOYLE',
            120,
        ],
        [
            'EGPF_FOYLE',
            'EGPF_LOMON',
            60,
        ],
        [
            'EGPF_FOYLE',
            'EGPF_ROBBO',
            60,
        ],
        [
            'EGPF_FOYLE',
            'EGPF_CLYDE_PTH',
            60,
        ],
        [
            'EGPF_LOMON',
            'EGPF_NORBO_LUSIV_TLA_TRN',
            60,
        ],
        [
            'EGPF_LOMON',
            'EGPF_FOYLE',
            120,
        ],
        [
            'EGPF_LOMON',
            'EGPF_LOMON',
            120,
        ],
        [
            'EGPF_LOMON',
            'EGPF_ROBBO',
            60,
        ],
        [
            'EGPF_LOMON',
            'EGPF_CLYDE_PTH',
            120,
        ],
        [
            'EGPF_ROBBO',
            'EGPF_NORBO_LUSIV_TLA_TRN',
            60,
        ],
        [
            'EGPF_ROBBO',
            'EGPF_FOYLE',
            120,
        ],
        [
            'EGPF_ROBBO',
            'EGPF_LOMON',
            120,
        ],
        [
            'EGPF_ROBBO',
            'EGPF_ROBBO',
            120,
        ],
        [
            'EGPF_ROBBO',
            'EGPF_CLYDE_PTH',
            120,
        ],
        [
            'EGPF_CLYDE_PTH',
            'EGPF_NORBO_LUSIV_TLA_TRN',
            60,
        ],
        [
            'EGPF_CLYDE_PTH',
            'EGPF_FOYLE',
            120,
        ],
        [
            'EGPF_CLYDE_PTH',
            'EGPF_LOMON',
            120,
        ],
        [
            'EGPF_CLYDE_PTH',
            'EGPF_ROBBO',
            60,
        ],
        [
            'EGPF_CLYDE_PTH',
            'EGPF_CLYDE_PTH',
            120,
        ],
        [
            'EGPH_SID',
            'EGPH_SID',
            120
        ],
        [
            'EGPK_SID',
            'EGPK_SID',
            120
        ],
        [
            'EGSS_NUGBO',
            'EGSS_NUGBO',
            240,
        ],
        [
            'EGSS_NUGBO',
            'EGSS_UTAVA_BKY',
            120,
        ],
        [
            'EGSS_NUGBO',
            'EGSS_SOUTH_EAST',
            60,
        ],
        [
            'EGSS_UTAVA_BKY',
            'EGSS_NUGBO',
            120,
        ],
        [
            'EGSS_UTAVA_BKY',
            'EGSS_UTAVA_BKY',
            120,
        ],
        [
            'EGSS_UTAVA_BKY',
            'EGSS_SOUTH_EAST',
            60,
        ],
        [
            'EGSS_SOUTH_EAST',
            'EGSS_NUGBO',
            60,
        ],
        [
            'EGSS_SOUTH_EAST',
            'EGSS_UTAVA_BKY',
            60,
        ],
        [
            'EGSS_SOUTH_EAST',
            'EGSS_SOUTH_EAST',
            120,
        ],
        [
            'EGVA_SID',
            'EGVA_SID',
            120
        ],
        [
            'EGWU_SID',
            'EGWU_SID',
            120
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $groups = DB::table('sid_departure_interval_groups')->get()->mapWithKeys(
            function ($group) {
                return [$group->key => $group->id];
            }
        );

        foreach (self::PAIRS as $pair) {
            DB::table('sid_departure_interval_group_sid_departure_interval_group')
                ->insert(
                    [
                        'lead_group_id' =>  $groups[$pair[0]],
                        'follow_group_id' =>  $groups[$pair[1]],
                        'interval' =>  $pair[2],
                    ]
                );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('sid_departure_interval_group_sid_departure_interval_group')->delete();
    }
}
