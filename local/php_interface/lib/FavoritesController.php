<?php
/**
 * Favorites Controller
 * Location: /local/php_interface/lib/FavoritesController.php
 * Version: 1.1.0
 * 
 * Modern D7 controller for handling favorites with proper namespace
 */

namespace Local\Controllers;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Error;

class FavoritesController extends Controller
{
    /**
     * Configure pre-filters for all actions
     * @return array
     */
    protected function getDefaultPreFilters()
    {
        return [
            new ActionFilter\Authentication(),
            new ActionFilter\HttpMethod([
                ActionFilter\HttpMethod::METHOD_POST
            ]),
            new ActionFilter\Csrf(),
        ];
    }

    /**
     * Toggle product in favorites
     * @param int $productId - Product ID
     * @return array
     */
    public function toggleAction(int $productId): array
    {
        global $USER;

        // Validate product ID
        if ($productId <= 0) {
            $this->addError(new Error('Invalid product ID.'));
            return [
                'success' => false,
                'message' => 'Неверный ID товара'
            ];
        }
        
        // Double check authorization (should be handled by filter)
        if (!$USER->IsAuthorized()) {
            $this->addError(new Error('User not authorized.'));
            return [
                'success' => false,
                'message' => 'Пользователь не авторизован'
            ];
        }

        try {
            $userId = $USER->GetID();
            
            // Get current favorites
            $rsUser = \CUser::GetByID($userId);
            $arUser = $rsUser->Fetch();
            $favorites = $arUser['UF_FAVORITES'] ?? [];
            
            if (!is_array($favorites)) {
                $favorites = [];
            }

            // Toggle favorite
            $key = array_search($productId, $favorites);
            $shouldAdd = ($key === false);

            if ($shouldAdd) {
                $favorites[] = $productId;
                $message = 'Товар добавлен в избранное';
            } else {
                unset($favorites[$key]);
                $message = 'Товар удален из избранного';
            }
            
            // Update user profile
            $user = new \CUser;
            $updateResult = $user->Update($userId, [
                'UF_FAVORITES' => array_values($favorites)
            ]);
            
            if (!$updateResult) {
                throw new \Exception($user->LAST_ERROR ?: 'Update failed');
            }
            
            return [
                'success' => true,
                'message' => $message,
                'data' => [
                    'inFavorites' => $shouldAdd,
                    'count' => count($favorites),
                    'productId' => $productId,
                ]
            ];
            
        } catch (\Exception $e) {
            $this->addError(new Error($e->getMessage()));
            
            return [
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Merge local favorites with server favorites
     * @param array $localFavorites - Product IDs from localStorage
     * @return array
     */
    public function mergeAction(array $localFavorites = []): array
    {
        global $USER;
        
        if (!$USER->IsAuthorized()) {
            return [
                'success' => false,
                'message' => 'Пользователь не авторизован'
            ];
        }

        try {
            $userId = $USER->GetID();

            // Get server favorites
            $rsUser = \CUser::GetByID($userId);
            $arUser = $rsUser->Fetch();
            $serverFavorites = $arUser['UF_FAVORITES'] ?? [];
            
            if (!is_array($serverFavorites)) {
                $serverFavorites = [];
            }

            // Merge and deduplicate
            $mergedFavorites = array_unique(array_merge($serverFavorites, $localFavorites));
            
            // Update
            $user = new \CUser;
            $updateResult = $user->Update($userId, [
                'UF_FAVORITES' => $mergedFavorites
            ]);
            
            if (!$updateResult) {
                throw new \Exception($user->LAST_ERROR ?: 'Merge failed');
            }
            
            return [
                'success' => true,
                'message' => 'Избранное синхронизировано',
                'data' => [
                    'count' => count($mergedFavorites)
                ]
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Ошибка синхронизации: ' . $e->getMessage()
            ];
        }
    }
}