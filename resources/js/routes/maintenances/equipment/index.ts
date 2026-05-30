import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\MaintenanceController::history
 * @see app/Http/Controllers/MaintenanceController.php:51
 * @route '/dashboard/maintenances/equipment/{equipment}/history-pdf'
 */
export const history = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: history.url(args, options),
    method: 'get',
})

history.definition = {
    methods: ["get","head"],
    url: '/dashboard/maintenances/equipment/{equipment}/history-pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MaintenanceController::history
 * @see app/Http/Controllers/MaintenanceController.php:51
 * @route '/dashboard/maintenances/equipment/{equipment}/history-pdf'
 */
history.url = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { equipment: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { equipment: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    equipment: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        equipment: typeof args.equipment === 'object'
                ? args.equipment.id
                : args.equipment,
                }

    return history.definition.url
            .replace('{equipment}', parsedArgs.equipment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MaintenanceController::history
 * @see app/Http/Controllers/MaintenanceController.php:51
 * @route '/dashboard/maintenances/equipment/{equipment}/history-pdf'
 */
history.get = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: history.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MaintenanceController::history
 * @see app/Http/Controllers/MaintenanceController.php:51
 * @route '/dashboard/maintenances/equipment/{equipment}/history-pdf'
 */
history.head = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: history.url(args, options),
    method: 'head',
})
const equipment = {
    history: Object.assign(history, history),
}

export default equipment