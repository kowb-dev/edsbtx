<?php
/**
 * Favorites Component Class
 * Location: /local/components/local/favorites/class.php
 * Version: 1.0.0
 * 
 * This component handles favorite products using modern Bitrix D7 AJAX
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;

class LocalFavoritesComponent extends \CBitrixComponent implements Controllerable
{
    /** @var ErrorCollection */
    protected $errorCollection;

    /**
     * Configure action filters for the component
     * @return array
     */
    public function configureActions()
    {
        return [
            'toggle' => [
                'prefilters' => [
                    new ActionFilter\Authentication(),
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST
                    ]),
                    new ActionFilter\Csrf(),
                ],
            ],
            'merge' => [
                'prefilters' => [
                    new ActionFilter\Authentication(),
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST
                    ]),
                    new ActionFilter\Csrf(),
                ],
            ],
        ];
    }

    /**
     * Constructor
     * @param mixed $component
     */
    public function __construct($component = null)
    {
        parent::__construct($component);
        $this->errorCollection = new ErrorCollection();
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
            $this->errorCollection->setError(new Error('Invalid product ID'));
            return [
                'success' => false,
                'message' => 'Неверный ID товара',
                'errors' => $this->errorCollection->toArray()
            ];
        }

        // Check if user is authorized (should be caught by filter, but double check)
        if (!$USER->IsAuthorized()) {
            $this->errorCollection->setError(new Error('User not authorized'));
            return [
                'success' => false,
                'message' => 'Пользователь не авторизован',
                'errors' => $this->errorCollection->toArray()
            ];
        }

        try {
            $userId = $USER->GetID();
            
            // Get current favorites from user profile
            $rsUser = \CUser::GetByID($userId);
            $arUser = $rsUser->Fetch();
            $favorites = $arUser['UF_FAVORITES'] ?? [];
            
            if (!is_array($favorites)) {
                $favorites = [];
            }

            // Check if product is in favorites
            $key = array_search($productId, $favorites);
            $shouldAdd = ($key === false);

            // Toggle favorite
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
                throw new \Exception($user->LAST_ERROR ?: 'Failed to update favorites');
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
            $this->errorCollection->setError(new Error($e->getMessage()));
            
            return [
                'success' => false,
                'message' => 'Ошибка при обновлении избранного: ' . $e->getMessage(),
                'errors' => $this->errorCollection->toArray()
            ];
        }
    }

    /**
     * Merge local favorites with server favorites
     * @param array $localFavorites - Array of product IDs from localStorage
     * @return array
     */
    public function mergeAction(array $localFavorites = []): array
    {
        global $USER;

        // Check authorization
        if (!$USER->IsAuthorized()) {
            return [
                'success' => false,
                'message' => 'Пользователь не авторизован'
            ];
        }

        try {
            $userId = $USER->GetID();
            
            // Get current favorites from server
            $rsUser = \CUser::GetByID($userId);
            $arUser = $rsUser->Fetch();
            $serverFavorites = $arUser['UF_FAVORITES'] ?? [];
            
            if (!is_array($serverFavorites)) {
                $serverFavorites = [];
            }

            // Merge and remove duplicates
            $mergedFavorites = array_unique(array_merge($serverFavorites, $localFavorites));
            
            // Update user profile
            $user = new \CUser;
            $updateResult = $user->Update($userId, [
                'UF_FAVORITES' => $mergedFavorites
            ]);
            
            if (!$updateResult) {
                throw new \Exception($user->LAST_ERROR ?: 'Failed to merge favorites');
            }
            
            return [
                'success' => true,
                'message' => 'Избранное успешно синхронизировано',
                'data' => [
                    'count' => count($mergedFavorites)
                ]
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Ошибка при синхронизации: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Execute component
     * @return mixed
     */
    public function executeComponent()
    {
        // This component is AJAX-only, no visual output needed
        return null;
    }
}
