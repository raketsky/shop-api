<?php
namespace App\Service;

use App\Entity\User;
use App\Exception\AppException;
use App\Repository\UserRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class UserService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string      $fullName
     * @param string      $address
     * @param string      $country
     * @param string      $city
     * @param string      $phone
     * @param string      $balance
     * @param string|null $zip
     * @param string|null $state
     * @return User
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(
        string $fullName,
        string $address,
        string $country,
        string $city,
        string $phone,
        string $balance,
        ?string $zip = null,
        ?string $state = null
    ): User {
        $user = new User();
        $user->setFullName($fullName);
        $user->setAddress($address);
        $user->setCountry($country);
        $user->setState($state);
        $user->setCity($city);
        $user->setZip($zip);
        $user->setPhone($phone);
        $user->setBalance($balance);

        $this->userRepository->save($user);

        return $user;
    }

    /**
     * @param User $user
     * @param int  $price
     * @return bool
     */
    public function checkBalance(User $user, int $price): bool
    {
        return $user->getBalance() >= $price;
    }

    /**
     * @param User $user
     * @param int  $amount
     * @return User
     * @throws AppException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function decBalance(User $user, int $amount): User
    {
        if (!$this->checkBalance($user, $amount)) {
            throw new AppException('Insufficient funds', 206);
        }
        $user->setBalance($user->getBalance() - $amount);
        $this->userRepository->save($user);

        return $user;
    }

    /**
     * @param int $id
     * @return User
     * @throws AppException
     */
    public function findByIdOrFail(int $id): User
    {
        $user = $this->userRepository->findOneById($id);
        if (!$user) {
            throw new AppException('User not found', 404);
        }

        return $user;
    }

    /**
     * @param User $user
     * @return array
     */
    public function toArray(User $user): array
    {
        return [
            'id' => $user->getId(),
            'full_name' => $user->getFullName(),
            'address' => $user->getAddress(),
            'country' => $user->getCountry(),
            'state' => $user->getState(),
            'city' => $user->getCity(),
            'zip' => $user->getZip(),
            'phone' => $user->getPhone(),
            'balance' => $user->getBalance(),
        ];
    }
}
